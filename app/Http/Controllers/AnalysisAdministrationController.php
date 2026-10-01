<?php

namespace App\Http\Controllers;

use App\Models\AnalysisAdministration;
use App\Models\LoanApplication;
use App\Models\User;
use App\Support\CreditAnalysis\AnalysisAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Administration: the fees charged with the loan. */
class AnalysisAdministrationController extends Controller
{
    public function update(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $analysis = AnalysisAccess::edit($user, $loanApplication);

        $data = array_map(
            fn (mixed $value): int => (int) $value,
            $request->validate(array_fill_keys(AnalysisAdministration::FEES, ['nullable', 'integer', 'min:0', 'max:999999999999'])),
        );

        AnalysisAdministration::query()->firstOrNew(['loan_analysis_id' => $analysis->id])->fill($data)->save();

        return back()->with('success', 'Administrasi berhasil disimpan.');
    }
}
