<?php

namespace App\Http\Controllers;

use App\Models\AnalysisMemorandum;
use App\Models\LoanApplication;
use App\Models\User;
use App\Support\CreditAnalysis\AnalysisAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Memorandum: the funds needed and the facility that is proposed. */
class AnalysisMemorandumController extends Controller
{
    public function update(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $analysis = AnalysisAccess::edit($user, $loanApplication);

        $money = ['nullable', 'integer', 'min:0', 'max:999999999999'];
        $rules = [
            ...array_fill_keys(AnalysisMemorandum::NEEDS, $money),
            ...array_fill_keys(array_map(fn (string $c): string => "{$c}_note", AnalysisMemorandum::NEEDS), ['nullable', 'string', 'max:255']),
            ...array_fill_keys(AnalysisMemorandum::RATES, ['nullable', 'numeric', 'min:0', 'max:100']),
            'proposed_amount' => $money,
            'term_months' => ['nullable', 'integer', 'min:0', 'max:600'],
            'before_disbursement' => ['nullable', 'string', 'max:255'],
            'additional_terms' => ['nullable', 'string', 'max:255'],
            'binding' => ['nullable', Rule::in(AnalysisMemorandum::BINDINGS)],
        ];

        $validated = $request->validate($rules, [], [
            'proposed_amount' => 'usulan plafon', 'term_months' => 'jangka waktu', 'admin_rate' => 'biaya admin', 'interest_rate' => 'suku bunga',
            'provision_rate' => 'biaya provisi', 'penalty_rate' => 'biaya penalti', 'before_disbursement' => 'syarat sebelum realisasi',
            'additional_terms' => 'syarat tambahan', 'binding' => 'pengikatan',
        ]);
        $data = Arr::map($validated, fn (mixed $value): mixed => is_string($value) ? Str::upper($value) : ($value ?? 0));

        AnalysisMemorandum::query()->firstOrNew(['loan_analysis_id' => $analysis->id])->fill($data)->save();

        return back()->with('success', 'Memorandum berhasil disimpan.');
    }
}
