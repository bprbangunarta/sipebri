<?php

namespace App\Http\Controllers;

use App\Models\AnalysisBusiness;
use App\Models\LoanApplication;
use App\Models\User;
use App\Support\CreditAnalysis\AnalysisAccess;
use App\Support\CreditAnalysis\AnalysisPayload;
use App\Support\CreditAnalysis\ItemSync;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/** The businesses of the applicant: one worksheet per business, of the type trade, farm, service or other. */
class AnalysisBusinessController extends Controller
{
    private const TEXTS = ['name', 'address', 'business_length', 'economy_sector', 'plant_type', 'business_kind'];

    public function store(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $analysis = AnalysisAccess::edit($user, $loanApplication);

        $data = $request->validate([
            'type' => ['required', Rule::in(AnalysisBusiness::TYPES)],
            'name' => ['required', 'string', 'max:150'],
        ], [], ['type' => 'tipe usaha', 'name' => 'nama usaha']);

        $business = $analysis->businesses()->create([
            'type' => $data['type'],
            'code' => AnalysisBusiness::nextCode($data['type']),
            'name' => Str::upper($data['name']),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        return to_route('credit-analysis.businesses.show', [$loanApplication, $business])->with('success', "Usaha {$business->code} dibuat. Lengkapi datanya.");
    }

    public function show(Request $request, LoanApplication $loanApplication, AnalysisBusiness $business): Response
    {
        /** @var User $user */
        $user = $request->user();
        AnalysisAccess::view($user, $loanApplication);
        $this->belongs($loanApplication, $business);

        $loanApplication->loadMissing(['installment', 'product']);

        return Inertia::render('credit-analysis/business', [
            'application' => [
                ...$loanApplication->only(['id', 'application_code', 'full_name', 'nik', 'requested_amount', 'requested_tenor']),
                'installment_label' => $loanApplication->installment?->name,
                'installment_period' => $loanApplication->installment->period_months ?? 0,
                'product_label' => $loanApplication->product ? "{$loanApplication->product->alias} : {$loanApplication->product->name}" : null,
            ],
            'business' => AnalysisPayload::business($business->load('items')),
            'canEdit' => $loanApplication->surveyor_id === $user->id && in_array($loanApplication->status->value, ['survey', 'analysis'], true),
            'options' => [
                'lengths' => AnalysisBusiness::LENGTHS,
                'sectors' => AnalysisBusiness::SECTORS,
                'plants' => AnalysisBusiness::PLANTS,
                'kinds' => AnalysisBusiness::KINDS,
            ],
        ]);
    }

    public function update(Request $request, LoanApplication $loanApplication, AnalysisBusiness $business): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        AnalysisAccess::edit($user, $loanApplication);
        $this->belongs($loanApplication, $business);

        $data = $request->validate($this->rules($request, $business), [], [
            'name' => 'nama usaha', 'business_length' => 'lama usaha', 'address' => 'alamat usaha',
            'items.*.name' => 'nama baris', 'items.*.qty' => 'jumlah', 'items.*.price' => 'nominal', 'items.*.sell_price' => 'harga jual',
        ]);

        $fields = Arr::map(Arr::except($data, ['items', 'groups']), function (mixed $value, string $key): mixed {
            if (! in_array($key, self::TEXTS, true)) {
                return $value ?? 0;
            }

            return $value === null || $value === '' ? null : Str::upper((string) $value);
        });

        $business->fill($fields);

        if (isset($data['groups'])) {
            ItemSync::replace($business->items(), $data['groups'], $data['items'] ?? [], ['name', 'qty', 'price', 'sell_price']);
            $business->unsetRelation('items');
        }

        $business->recalculate();
        $business->updated_by = $user->id;
        $business->save();

        return back()->with('success', 'Data usaha berhasil disimpan.');
    }

    public function destroy(Request $request, LoanApplication $loanApplication, AnalysisBusiness $business): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        AnalysisAccess::edit($user, $loanApplication);
        $this->belongs($loanApplication, $business);

        $business->delete();

        return to_route('credit-analysis.show', $loanApplication)->with('success', 'Usaha berhasil dihapus.');
    }

    private function belongs(LoanApplication $loan, AnalysisBusiness $business): void
    {
        abort_unless($business->loanAnalysis->loan_application_id === $loan->id, 404);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(Request $request, AnalysisBusiness $business): array
    {
        $groups = $request->input('groups') ?: AnalysisBusiness::GROUPS;
        $money = ['sometimes', 'nullable', 'integer', 'min:0', 'max:999999999999'];

        $common = [
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'business_length' => ['sometimes', 'nullable', Rule::in(AnalysisBusiness::LENGTHS)],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'projection_addition' => $money,
            'groups' => ['sometimes', 'array'],
            'groups.*' => [Rule::in(AnalysisBusiness::GROUPS)],
            'items' => ['sometimes', 'array', 'max:100'],
            'items.*.group' => ['required', Rule::in($groups)],
            'items.*.name' => ['required', 'string', 'max:150'],
            'items.*.qty' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'items.*.price' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'items.*.sell_price' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
        ];

        $typed = match ($business->type) {
            'trade' => [
                'daily_purchase' => $money, 'cost_of_goods' => $money,
                ...array_fill_keys(AnalysisBusiness::TRADE_COSTS, $money),
            ],
            'farm' => [
                'economy_sector' => ['sometimes', 'nullable', Rule::in(AnalysisBusiness::SECTORS)],
                'plant_type' => ['sometimes', 'nullable', Rule::in(AnalysisBusiness::PLANTS)],
                'area_own' => $money, 'area_rent' => $money, 'area_pawn' => $money,
                'harvest_quintals' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:99999999'],
                'price_per_quintal' => $money, 'addition_result' => $money, 'other_bank_loan' => $money, 'principal_installment' => $money,
                ...array_fill_keys(AnalysisBusiness::FARM_COSTS, $money),
            ],
            'service' => ['service_income' => $money, 'vehicle_tax' => $money, 'other_expense' => $money],
            default => ['business_kind' => ['sometimes', 'nullable', Rule::in(AnalysisBusiness::KINDS)]],
        };

        return [...$common, ...$typed];
    }
}
