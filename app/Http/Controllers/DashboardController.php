<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\User;
use App\Support\Navigation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Credit overview. Follows the visibility rule of LoanApplicationPolicy: people who work on files at a later
 * stage (scheduling, survey, analysis) see every file, everybody else only the files they opened.
 */
class DashboardController extends Controller
{
    /** Statuses of a file that is being worked on (submitted, not yet decided). */
    private const IN_PROCESS = [LoanStatus::Submitted, LoanStatus::Scheduling, LoanStatus::Survey, LoanStatus::Analysis, LoanStatus::Committee];

    public function __invoke(Request $request): Response|RedirectResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if (! $user->can('dashboard.view')) {
            $href = Navigation::firstHref($user);

            abort_if($href === null, 403, 'Peran Anda belum punya akses ke halaman mana pun.');

            return redirect($href);
        }

        $all = $user->hasAnyPermission(['scheduling.view', 'surveys.view', 'credit-analysis.view']);
        $inProcess = array_map(fn (LoanStatus $s): string => $s->value, self::IN_PROCESS);

        return Inertia::render('dashboard', [
            'scope' => $all ? 'all' : 'mine',
            'stats' => [
                'total' => $this->files($user, $all)->count(),
                'in_process' => $this->files($user, $all)->whereIn('status', $inProcess)->count(),
                'drafts' => $this->files($user, $all)->where('status', LoanStatus::Draft->value)->count(),
                'this_month' => $this->files($user, $all)->where('application_date', '>=', now()->startOfMonth()->toDateString())->count(),
                'amount_in_process' => (int) $this->files($user, $all)->whereIn('status', $inProcess)->sum('requested_amount'),
            ],
            'byStatus' => $this->byStatus($user, $all),
            'byProduct' => $this->byRelation($user, $all, 'products', 'product_id', 'alias'),
            'byOffice' => $this->byRelation($user, $all, 'offices', 'office_id', 'alias'),
            'byMonth' => $this->byMonth($user, $all),
            'upcomingSurveys' => $this->files($user, $all)->with('surveyor:id,name')
                ->where('status', LoanStatus::Scheduling->value)->where('survey_date', '>=', now()->toDateString())
                ->orderBy('survey_date')->limit(6)->get()
                ->map(fn (LoanApplication $l): array => [
                    'id' => $l->id, 'code' => $l->application_code, 'name' => $l->full_name,
                    'date' => $l->survey_date?->toDateString(), 'surveyor' => $l->surveyor?->name,
                ]),
            'recent' => $this->files($user, $all)->latest('application_date')->latest('id')->limit(6)->get()
                ->map(fn (LoanApplication $l): array => [
                    'id' => $l->id, 'code' => $l->application_code, 'name' => $l->full_name, 'amount' => $l->requested_amount,
                    'date' => $l->application_date->toDateString(), 'status' => $l->status->label(), 'tone' => $l->status->tone(),
                ]),
        ]);
    }

    /**
     * @return Builder<LoanApplication>
     */
    private function files(User $user, bool $all): Builder
    {
        return LoanApplication::query()->when(! $all, fn (Builder $q) => $q->where('created_by', $user->id));
    }

    /**
     * @return list<array{name: string, total: int}>
     */
    private function byStatus(User $user, bool $all): array
    {
        $counts = $this->files($user, $all)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');

        return array_map(fn (LoanStatus $s): array => ['name' => $s->label(), 'total' => (int) ($counts[$s->value] ?? 0)], LoanStatus::cases());
    }

    /**
     * Files per record of a reference table, biggest first (only records that have files).
     *
     * @return list<array{name: string, total: int}>
     */
    private function byRelation(User $user, bool $all, string $table, string $foreignKey, string $column): array
    {
        $rows = DB::table('loan_applications')
            ->whereNull('loan_applications.deleted_at')
            ->when(! $all, fn ($q) => $q->where('loan_applications.created_by', $user->id))
            ->join($table, "{$table}.id", '=', "loan_applications.{$foreignKey}")
            ->select("{$table}.{$column} as name", DB::raw('count(*) as total'))
            ->groupBy("{$table}.id", "{$table}.{$column}")
            ->orderByDesc('total')->orderBy('name')->limit(8)->get()
            ->map(fn (object $row): array => ['name' => (string) $row->name, 'total' => (int) $row->total]);

        return array_values($rows->all());
    }

    /**
     * New files for each of the last 12 months, oldest first.
     *
     * @return list<array{month: string, total: int}>
     */
    private function byMonth(User $user, bool $all): array
    {
        $start = now()->startOfMonth()->subMonths(11);
        $counts = $this->files($user, $all)->where('application_date', '>=', $start->toDateString())
            ->pluck('application_date')
            ->countBy(fn ($date): string => $date->format('Y-m'));

        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $month = $start->addMonths($i);
            $months[] = ['month' => $month->translatedFormat('M y'), 'total' => (int) ($counts[$month->format('Y-m')] ?? 0)];
        }

        return $months;
    }
}
