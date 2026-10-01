<?php

namespace App\Support\Approvals;

use App\Enums\LoanStatus;
use App\Models\CommitteeTier;
use App\Models\LoanApplication;
use App\Models\LoanApproval;
use App\Models\Method;
use App\Models\ProductParameter;
use App\Models\User;
use App\Support\Committee;
use App\Support\CreditAnalysis\RepaymentCapacity;
use App\Support\Notify;
use Illuminate\Support\Facades\DB;

/**
 * The committee route of a file. `open()` works out, from the committee rules and the proposed amount, which tiers have to
 * act and writes them as pending steps; `decide()` fills in the next pending step and either passes the file up or ends it.
 *
 * The steps are written once, when the analysis is submitted, so the route a file followed does not change when the
 * committee rules are edited later.
 */
final class ApprovalFlow
{
    /**
     * What the committee sees as the proposal: the analyst's memorandum (or the application, where it is empty), the
     * capacity of the applicant and the loan as a share of it.
     *
     * @return array<string, mixed>
     */
    public static function basis(LoanApplication $loan): array
    {
        $loan->loadMissing(['analysis.memorandum', 'analysis.finance.items', 'method', 'collaterals']);
        $memorandum = $loan->analysis?->memorandum;
        $parameter = ProductParameter::query()->where('product_id', $loan->product_id)->first();

        $capacity = max(0, $loan->analysis?->finance?->metrics()['monthly_balance'] ?? 0);
        $threshold = (float) ($parameter?->rc_threshold ?: RepaymentCapacity::FALLBACK_THRESHOLD);

        $amount = $memorandum?->proposed_amount ?: $loan->requested_amount;
        $tenor = $memorandum?->term_months ?: $loan->requested_tenor;
        $rate = (float) ($memorandum?->interest_rate ?: $loan->interest_rate ?: 0);
        $max = RepaymentCapacity::maxAmount($capacity, $threshold, $rate, $tenor, (string) $loan->method?->name);

        return [
            'capacity' => $capacity,
            'rc_threshold' => $threshold,
            'max_amount' => $max,
            'rc_ratio' => RepaymentCapacity::ratio($amount, $max),
            'amount' => $amount,
            'tenor' => $tenor,
            'interest_rate' => $rate,
            'provision_rate' => (float) ($memorandum?->provision_rate ?: $parameter?->provision_rate ?: 0),
            'admin_rate' => (float) ($memorandum?->admin_rate ?: $parameter?->admin_rate ?: 0),
            'method_id' => $loan->method_id,
            'method_label' => $loan->method?->name,
            'appraisal_total' => (int) $loan->collaterals->sum('appraisal_value'),
        ];
    }

    /**
     * Put the file on its committee route. Returns why it cannot be done (no route for the product, nobody to decide), or
     * null when the steps were written.
     */
    public static function open(LoanApplication $loan, User $analyst): ?string
    {
        $basis = self::basis($loan);
        $applicant = $loan->committee_conflict_user_id ? User::query()->find($loan->committee_conflict_user_id) : null;
        $route = Committee::resolve($loan->product_id, $loan->committeePath?->condition, $basis['amount'], $applicant);

        if (! $route['found']) {
            return (string) $route['message'];
        }

        if ($route['decider'] === null) {
            return 'Jalur komite tidak punya pemutus untuk berkas ini: '.implode(' ', $route['warnings']);
        }

        $steps = array_values(array_filter($route['chain'], fn (array $row): bool => in_array($row['status'], ['escalate', 'decider'], true)));

        DB::transaction(function () use ($loan, $analyst, $steps, $route, $basis): void {
            $loan->approvals()->each(fn (LoanApproval $step) => $step->delete());

            foreach ($steps as $row) {
                $step = $loan->approvals()->create([
                    'committee_tier_id' => $row['id'],
                    'sort' => $row['sort'],
                    'tier_label' => $row['label'],
                    'role' => $row['role'],
                    'is_individual' => $row['individual'],
                    'waived' => $row['status'] === 'decider' && $route['exception'] !== null,
                ]);

                // The analyst's own level only passes the file on by submitting it: no separate decision is asked of them.
                if ($row['individual'] && $row['status'] === 'escalate') {
                    self::record($step, $analyst, LoanApproval::FORWARD, [
                        'method_id' => $basis['method_id'], 'amount' => $basis['amount'], 'tenor' => $basis['tenor'],
                        'interest_rate' => $basis['interest_rate'], 'provision_rate' => $basis['provision_rate'], 'admin_rate' => $basis['admin_rate'],
                        'note' => 'Analisa diajukan.',
                    ], $basis);
                }
            }

            $loan->update(['committee_exception' => $route['exception']]);
        });

        return null;
    }

    /** The step that waits for a decision now. */
    public static function pending(LoanApplication $loan): ?LoanApproval
    {
        return $loan->approvals()->whereNull('decision')->orderBy('sort')->first();
    }

    /**
     * What the person at this step may decide.
     *
     * @return list<string>
     */
    public static function allowed(LoanApproval $step, int $amount): array
    {
        if ($step->waived) {
            return [LoanApproval::APPROVE, LoanApproval::REJECT, LoanApproval::CANCEL];
        }

        $tier = $step->tier;

        if ($tier === null) {
            return [];
        }

        $out = [];

        if ($tier->can_escalate && self::tierAbove($step) !== null) {
            $out[] = LoanApproval::FORWARD;
        }

        if ($tier->can_approve && ($tier->max_amount === null || $amount <= $tier->max_amount)) {
            $out[] = LoanApproval::APPROVE;
        }

        if ($tier->can_reject) {
            $out[] = LoanApproval::REJECT;
        }

        if ($tier->can_cancel) {
            $out[] = LoanApproval::CANCEL;
        }

        return $out;
    }

    public static function mayDecide(User $user, LoanApproval $step, LoanApplication $loan): bool
    {
        if (! $step->isPending() || $loan->status !== LoanStatus::Committee || $user->id === $loan->committee_conflict_user_id) {
            return false;
        }

        if ($step->is_individual) {
            return $loan->surveyor_id === $user->id;
        }

        return $user->hasRole('Super Admin') || $user->hasRole($step->role);
    }

    /**
     * Record the decision of the pending step. A forward moves the file to the next step (adding the next tier above when the
     * route has none left); anything else ends the file.
     *
     * @param  array<string, mixed>  $data  decision, method_id, amount, tenor, interest_rate, provision_rate, admin_rate, note
     */
    public static function decide(LoanApplication $loan, LoanApproval $step, User $user, array $data): void
    {
        $basis = self::basis($loan);

        DB::transaction(function () use ($loan, $step, $user, $data, $basis): void {
            self::record($step, $user, $data['decision'], $data, $basis);

            if ($data['decision'] === LoanApproval::FORWARD) {
                self::forward($loan, $step);

                return;
            }

            self::finish($loan, $step, $user, $data);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $basis
     */
    private static function record(LoanApproval $step, User $user, string $decision, array $data, array $basis): void
    {
        $method = isset($data['method_id']) ? Method::query()->whereKey($data['method_id'])->first() : null;
        $max = RepaymentCapacity::maxAmount($basis['capacity'], $basis['rc_threshold'], (float) $data['interest_rate'], (int) $data['tenor'], (string) $method?->name);

        $step->update([
            'decision' => $decision,
            'decided_by' => $user->id,
            'decided_by_name' => $user->name,
            'method_id' => $data['method_id'] ?? null,
            'amount' => $data['amount'],
            'tenor' => $data['tenor'],
            'interest_rate' => $data['interest_rate'],
            'provision_rate' => $data['provision_rate'],
            'admin_rate' => $data['admin_rate'],
            'max_amount' => $max,
            'rc_ratio' => RepaymentCapacity::ratio((int) $data['amount'], $max),
            'note' => ($data['note'] ?? null) ?: null,
            'decided_at' => now(),
        ]);
    }

    private static function forward(LoanApplication $loan, LoanApproval $from): void
    {
        $next = $loan->approvals()->whereNull('decision')->where('sort', '>', $from->sort)->orderBy('sort')->first();

        if ($next === null) {
            $tier = self::tierAbove($from);
            abort_if($tier === null, 422, 'Tidak ada jenjang di atasnya.');

            $next = $loan->approvals()->create(['committee_tier_id' => $tier->id, 'sort' => $tier->sort, 'tier_label' => $tier->label, 'role' => $tier->role, 'is_individual' => $tier->is_individual]);
        }

        Notify::toRole($next->role, 'Berkas menunggu keputusan Anda', 'Persetujuan', "Berkas {$loan->application_code} - {$loan->full_name} diteruskan ke jenjang {$next->tier_label}.", "/approvals/{$loan->id}");
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function finish(LoanApplication $loan, LoanApproval $step, User $user, array $data): void
    {
        $approved = $data['decision'] === LoanApproval::APPROVE;
        $status = match ($data['decision']) {
            LoanApproval::APPROVE => LoanStatus::Approved,
            LoanApproval::REJECT => LoanStatus::Rejected,
            default => LoanStatus::Cancelled,
        };

        $loan->update([
            'status' => $status,
            'decided_at' => now(),
            'decided_by' => $user->id,
            'decision_note' => ($data['note'] ?? null) ?: null,
            'approved_amount' => $approved ? $data['amount'] : 0,
            'approved_tenor' => $approved ? $data['tenor'] : 0,
            'approved_rate' => $approved ? $data['interest_rate'] : 0,
            'rc_ratio' => $step->rc_ratio,
        ]);

        $label = LoanApproval::LABELS[$data['decision']];
        $level = $approved ? 'success' : 'warning';
        $body = "Berkas {$loan->application_code} - {$loan->full_name} {$label} oleh {$step->tier_label}.";

        foreach (array_filter([$loan->surveyor, $loan->supervisor]) as $person) {
            if ($person->id !== $user->id) {
                Notify::toUser($person, "Keputusan komite: {$label}", 'Persetujuan', $body, '/approvals', $level);
            }
        }
    }

    private static function tierAbove(LoanApproval $step): ?CommitteeTier
    {
        $tier = $step->tier;

        if ($tier === null) {
            return null;
        }

        $applicantId = $step->loanApplication->committee_conflict_user_id;

        return $tier->path->tiers()->where('sort', '>', $tier->sort)->get()
            ->first(fn (CommitteeTier $t): bool => $applicantId === null || User::role($t->role)->whereKeyNot($applicantId)->exists());
    }
}
