<?php

namespace App\Http\Controllers;

use App\Models\AnalysisFinance;
use App\Models\LoanAnalysis;
use App\Models\LoanApplication;
use App\Models\User;
use App\Support\CreditAnalysis\AnalysisAccess;
use App\Support\CreditAnalysis\ItemSync;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/** Finance analysis (household costs and obligations) and ownership analysis (what the applicant owns). */
class AnalysisFinanceController extends Controller
{
    public function updateFinance(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        AnalysisAccess::edit($user, $loanApplication);

        $money = ['nullable', 'integer', 'min:0', 'max:999999999999'];
        $data = $request->validate([
            ...array_fill_keys(AnalysisFinance::HOUSEHOLD, $money),
            'items' => ['nullable', 'array', 'max:20'],
            'items.*.name' => ['required', 'string', 'max:150'],
            'items.*.amount' => $money,
        ], [], ['items.*.name' => 'nama kewajiban', 'items.*.amount' => 'nominal kewajiban']);

        $error = AnalysisAccess::change($user, $loanApplication, function (LoanAnalysis $analysis) use ($data): void {
            $finance = AnalysisFinance::query()->firstOrCreate(['loan_analysis_id' => $analysis->id]);
            $finance->fill(Arr::map(Arr::except($data, 'items'), fn (mixed $v): int => (int) $v))->save();

            ItemSync::replace($finance->items(), ['obligation'], array_map(fn (array $row): array => [...$row, 'group' => 'obligation'], $data['items'] ?? []), ['name', 'amount']);
        });

        return $error ? back()->with('error', $error) : back()->with('success', 'Analisa keuangan berhasil disimpan.');
    }

    public function updateOwnership(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        AnalysisAccess::edit($user, $loanApplication);

        $rules = collect(AnalysisFinance::ASSETS)->map(fn (array $options): array => ['nullable', Rule::in($options)])->all();
        $data = $request->validate([
            ...$rules,
            'items' => ['nullable', 'array', 'max:20'],
            'items.*.name' => ['required', 'string', 'max:150'],
        ], [], ['items.*.name' => 'nama harta']);

        $error = AnalysisAccess::change($user, $loanApplication, function (LoanAnalysis $analysis) use ($data): void {
            $finance = AnalysisFinance::query()->firstOrCreate(['loan_analysis_id' => $analysis->id]);
            $finance->fill(Arr::except($data, 'items'))->save();

            ItemSync::replace($finance->items(), ['asset'], array_map(fn (array $row): array => [...$row, 'group' => 'asset'], $data['items'] ?? []), ['name']);
        });

        return $error ? back()->with('error', $error) : back()->with('success', 'Analisa kepemilikan berhasil disimpan.');
    }
}
