<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AnalysisBusiness;
use App\Models\AnalysisBusinessItem;
use App\Models\LoanApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Analisa Usaha — bagian 1 lembar Analisa Kredit.
 * Empat tipe usaha (perdagangan/pertanian/jasa/lainnya) memakai satu tabel;
 * kolom hasil hitung tidak pernah diinput (lihat AnalysisBusiness::metrics()).
 */
class AnalysisBusinessController extends Controller
{
    private const LABEL = 'Analisa Kredit';

    public function store(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeAnalyst($request, $loanApplication);

        $data = $request->validate(
            [
                'type' => ['required', Rule::in(AnalysisBusiness::TYPES)],
                'name' => ['required', 'string', 'max:150'],
            ],
            [],
            ['type' => 'tipe usaha', 'name' => 'nama usaha'],
        );

        $business = AnalysisBusiness::create([
            'loan_application_id' => $loanApplication->id,
            'type' => $data['type'],
            'code' => AnalysisBusiness::nextCode($data['type']),
            'name' => Str::upper($data['name']),
        ]);

        ActivityLog::record(
            "Menambah usaha {$business->code} pada berkas {$loanApplication->application_code}",
            self::LABEL,
            'success',
            $business,
        );

        return redirect()
            ->route('analysis-simulation.businesses.show', [$loanApplication, $business])
            ->with('success', "Usaha {$business->code} dibuat. Lengkapi datanya.");
    }

    public function show(Request $request, LoanApplication $loanApplication, AnalysisBusiness $business): Response
    {
        $this->authorizeAnalyst($request, $loanApplication);

        return Inertia::render('AnalysisBusinessDetail', [
            'application' => [
                'id' => $loanApplication->id,
                'application_code' => $loanApplication->application_code,
                'full_name' => $loanApplication->full_name,
                'nik' => $loanApplication->nik,
                'status' => $loanApplication->status,
                'requested_amount' => (int) $loanApplication->requested_amount,
                'requested_tenor' => (int) $loanApplication->requested_tenor,
                'installment_label' => $loanApplication->installment?->name,
                'installment_period' => (int) ($loanApplication->installment?->period_months ?? 0),
                'product_label' => $loanApplication->product
                    ? "{$loanApplication->product->alias} : {$loanApplication->product->name}"
                    : null,
            ],
            'business' => $this->payload($business),
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
        $this->authorizeAnalyst($request, $loanApplication);

        $data = $request->validate($this->rules($request, $business), [], $this->labels());

        // Kolom teks/pilihan boleh null; hanya kolom angka yang jatuh ke 0.
        $texts = ['name', 'address', 'business_length', 'economy_sector', 'plant_type', 'business_kind'];

        $fields = collect($data)->except(['items', 'groups'])->map(function ($value, $key) use ($texts) {
            if (! in_array($key, $texts, true)) {
                return $value ?? 0;
            }

            return $value === null || $value === '' ? null : Str::upper((string) $value);
        })->all();

        $business->fill($fields);

        if (isset($data['groups'])) {
            AnalysisBusinessItem::where('analysis_business_id', $business->id)
                ->whereIn('group', $data['groups'])->delete();

            foreach (array_values($data['items'] ?? []) as $sort => $item) {
                AnalysisBusinessItem::create([
                    'analysis_business_id' => $business->id,
                    'group' => $item['group'],
                    'name' => Str::upper($item['name']),
                    'qty' => $item['qty'] ?? 0,
                    'price' => $item['price'] ?? 0,
                    'sell_price' => $item['sell_price'] ?? 0,
                    'sort' => $sort,
                ]);
            }

            $business->unsetRelation('items');
        }

        $business->recalculate();
        $business->save();

        return back()->with('success', 'Data usaha disimpan.');
    }

    public function destroy(Request $request, LoanApplication $loanApplication, AnalysisBusiness $business): RedirectResponse
    {
        $this->authorizeAnalyst($request, $loanApplication);

        ActivityLog::record(
            "Menghapus usaha {$business->code} pada berkas {$loanApplication->application_code}",
            self::LABEL,
            'warning',
            $business,
        );

        $business->delete();

        return redirect()
            ->route('analysis-simulation.show', $loanApplication)
            ->with('success', 'Usaha dihapus.');
    }

    public function payload(AnalysisBusiness $business): array
    {
        return [
            ...$business->only([
                'id', 'type', 'code', 'name', 'business_length', 'address',
                'daily_purchase', 'cost_of_goods', ...AnalysisBusiness::TRADE_COSTS,
                'economy_sector', 'plant_type', 'area_own', 'area_rent', 'area_pawn',
                'harvest_kw', 'price_per_kw', ...AnalysisBusiness::FARM_COSTS,
                'addition_result', 'other_bank_loan', 'principal_installment',
                'service_income', 'vehicle_tax', 'other_expense',
                'business_kind', 'projection_addition',
                'revenue', 'expense', 'net_profit', 'monthly_income',
            ]),
            'items' => $business->items->map(fn (AnalysisBusinessItem $i) => [
                'id' => $i->id,
                'group' => $i->group,
                'name' => $i->name,
                'qty' => $i->qty,
                'price' => (int) $i->price,
                'sell_price' => (int) $i->sell_price,
            ])->values()->all(),
            'metrics' => $business->metrics(),
            'updated_by' => $business->updated_by ?? $business->created_by,
            'updated_at' => $business->updated_at?->translatedFormat('d M Y H:i'),
        ];
    }

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
            'PERDAGANGAN' => array_merge(
                ['daily_purchase' => $money, 'cost_of_goods' => $money],
                array_fill_keys(AnalysisBusiness::TRADE_COSTS, $money),
            ),
            'PERTANIAN' => array_merge(
                [
                    'economy_sector' => ['sometimes', 'nullable', Rule::in(AnalysisBusiness::SECTORS)],
                    'plant_type' => ['sometimes', 'nullable', Rule::in(AnalysisBusiness::PLANTS)],
                    'area_own' => $money,
                    'area_rent' => $money,
                    'area_pawn' => $money,
                    'harvest_kw' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:99999999'],
                    'price_per_kw' => $money,
                    'addition_result' => $money,
                    'other_bank_loan' => $money,
                    'principal_installment' => $money,
                ],
                array_fill_keys(AnalysisBusiness::FARM_COSTS, $money),
            ),
            'JASA' => [
                'service_income' => $money,
                'vehicle_tax' => $money,
                'other_expense' => $money,
            ],
            default => ['business_kind' => ['sometimes', 'nullable', Rule::in(AnalysisBusiness::KINDS)]],
        };

        return array_merge($common, $typed);
    }

    private function labels(): array
    {
        return [
            'name' => 'nama usaha',
            'business_length' => 'lama usaha',
            'address' => 'alamat usaha',
            'items.*.name' => 'nama baris',
            'items.*.qty' => 'jumlah',
            'items.*.price' => 'nominal',
            'items.*.sell_price' => 'harga jual',
        ];
    }

    private function authorizeAnalyst(Request $request, LoanApplication $r): void
    {
        if ($r->surveyor_id !== $request->user()->id) {
            throw new NotFoundHttpException('Berkas ini bukan penugasan Anda.');
        }
    }
}
