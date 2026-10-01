<?php

namespace App\Http\Controllers;

use App\Models\AnalysisFiveC;
use App\Models\AnalysisQualitative;
use App\Models\LoanAnalysis;
use App\Models\LoanApplication;
use App\Models\User;
use App\Support\CreditAnalysis\AnalysisAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** The scored and descriptive assessments of the applicant and the business: 5C and qualitative. */
class AnalysisAssessmentController extends Controller
{
    /** 5C: only the scores are entered; the evaluation is worked out by the system. */
    public function updateFiveC(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        AnalysisAccess::edit($user, $loanApplication);

        $rules = [];

        foreach (AnalysisFiveC::ASPECTS as $aspects) {
            foreach ($aspects as $column => $scale) {
                $rules[$column] = ['nullable', 'integer', 'min:0', "max:{$scale}"];
            }
        }

        $data = $request->validate($rules);
        $error = AnalysisAccess::change($user, $loanApplication, fn (LoanAnalysis $analysis) => AnalysisFiveC::query()->firstOrNew(['loan_analysis_id' => $analysis->id])->fill($data)->save());

        return $error ? back()->with('error', $error) : back()->with('success', 'Analisa 5C berhasil disimpan.');
    }

    public function updateQualitative(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        AnalysisAccess::edit($user, $loanApplication);

        $rules = [];

        foreach (AnalysisQualitative::SCORES as $column => $scale) {
            $rules[$column] = ['nullable', 'integer', 'min:1', "max:{$scale}"];
        }

        foreach (AnalysisQualitative::CHOICES as $column => $choices) {
            $rules[$column] = ['nullable', Rule::in($choices)];
        }

        foreach (AnalysisQualitative::TEXTS as $column) {
            $rules[$column] = ['nullable', 'string', 'max:255'];
        }

        foreach (AnalysisQualitative::NOTES as $column) {
            $rules[$column] = ['nullable', 'string', 'max:2000'];
        }

        $data = Arr::map($request->validate($rules), fn (mixed $value): mixed => is_string($value) ? Str::upper($value) : $value);

        $error = AnalysisAccess::change($user, $loanApplication, fn (LoanAnalysis $analysis) => AnalysisQualitative::query()->firstOrNew(['loan_analysis_id' => $analysis->id])->fill($data)->save());

        return $error ? back()->with('error', $error) : back()->with('success', 'Analisa kualitatif berhasil disimpan.');
    }
}
