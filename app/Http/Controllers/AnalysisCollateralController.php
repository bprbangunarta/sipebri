<?php

namespace App\Http\Controllers;

use App\Models\AnalysisCollateral;
use App\Models\LoanApplication;
use App\Models\User;
use App\Support\CreditAnalysis\AnalysisAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Collateral analysis: the examination report of each collateral of the file. */
class AnalysisCollateralController extends Controller
{
    public function update(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $analysis = AnalysisAccess::edit($user, $loanApplication);

        $ids = $loanApplication->collaterals()->pluck('collaterals.id')->all();
        $money = ['nullable', 'integer', 'min:0', 'max:999999999999'];

        $data = $request->validate([
            'rows' => ['required', 'array'],
            'rows.*.collateral_id' => ['required', Rule::in($ids)],
            'rows.*.kind' => ['required', Rule::in(AnalysisCollateral::KINDS)],
            'rows.*.brand' => ['nullable', 'string', 'max:100'],
            'rows.*.vehicle_type' => ['nullable', 'string', 'max:100'],
            'rows.*.year' => ['nullable', 'digits:4'],
            'rows.*.chassis_number' => ['nullable', 'string', 'max:50'],
            'rows.*.engine_number' => ['nullable', 'string', 'max:50'],
            'rows.*.plate_number' => ['nullable', 'string', 'max:20'],
            'rows.*.color' => ['nullable', 'string', 'max:50'],
            'rows.*.land_area' => ['nullable', 'integer', 'min:0', 'max:99999999'],
            'rows.*.location' => ['nullable', 'string', 'max:255'],
            'rows.*.market_value' => $money,
            'rows.*.appraisal_value' => $money,
            'rows.*.notes' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'rows.*.year' => 'tahun', 'rows.*.land_area' => 'luas', 'rows.*.market_value' => 'nilai pasar', 'rows.*.appraisal_value' => 'nilai taksasi',
        ]);

        foreach ($data['rows'] as $row) {
            $values = Arr::map(Arr::except($row, 'collateral_id'), fn (mixed $value, string $key): mixed => $key === 'kind' ? $value : (is_string($value) ? Str::upper($value) : ($value ?? 0)));

            AnalysisCollateral::query()->updateOrCreate(['loan_analysis_id' => $analysis->id, 'collateral_id' => $row['collateral_id']], $values);
        }

        return back()->with('success', 'Analisa agunan berhasil disimpan.');
    }
}
