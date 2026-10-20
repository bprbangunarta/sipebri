<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Models\CollateralAppraisal;
use App\Support\Appraisal\AppraisalFigures;
use App\Support\Appraisal\AppraisalFinalisation;
use App\Support\Appraisal\AppraisalGuard;
use App\Support\Appraisal\AppraisalRules;
use App\Support\Appraisal\AppraisalSaver;
use App\Support\Appraisal\AppraisalTemplates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Saving the answers of one section of an appraisal, and finalising the appraisal.
 */
class CollateralAppraisalSectionController extends Controller
{
    public function update(Request $request, CollateralAppraisal $collateralAppraisal, string $section): RedirectResponse
    {
        if ($refused = AppraisalGuard::refuseIfFinal($collateralAppraisal)) {
            return $refused;
        }

        $template = AppraisalTemplates::of($collateralAppraisal);
        abort_unless(collect($template->sections())->contains('key', $section), 404);

        $request->validate([
            'answers' => ['required', 'array', 'max:400'],
            'answers.*.key' => ['required', 'string', 'max:80'],
            'answers.*.value' => ['nullable'],
            'answers.*.note' => ['nullable', 'string', 'max:'.AppraisalRules::NOTE_MAX],
        ]);

        $items = collect(AppraisalTemplates::items($template))->where('section', $section)->filter(fn (array $item): bool => AppraisalRules::answerable($item))->keyBy('key');
        $answers = $this->answers($request, $collateralAppraisal, $items->all());

        if (AppraisalSaver::save($collateralAppraisal, $template, $section, $answers)) {
            return back()->with('success', 'Bagian penilaian disimpan.');
        }

        return back()->with('success', 'Tidak ada perubahan.');
    }

    public function finalize(Request $request, CollateralAppraisal $collateralAppraisal): RedirectResponse
    {
        if ($refused = AppraisalGuard::refuseIfFinal($collateralAppraisal)) {
            return $refused;
        }

        $template = AppraisalTemplates::of($collateralAppraisal);
        AppraisalFigures::refresh($collateralAppraisal, $template);
        $blockers = AppraisalFinalisation::blockers($collateralAppraisal, $template);

        if ($blockers !== []) {
            throw ValidationException::withMessages(['finalize' => implode(' ', $blockers)]);
        }

        $collateralAppraisal->update(['status' => CollateralAppraisal::FINAL, 'finalized_at' => now(), 'finalized_by' => $request->user()->name]);
        Audit::record('collateral_appraisals.finalized', 'collateral_appraisals', 'finalized', $collateralAppraisal, context: [
            'decision' => $collateralAppraisal->decision, 'market_value' => $collateralAppraisal->market_value, 'liquidation_value' => $collateralAppraisal->liquidation_value,
        ]);

        return back()->with('success', 'Penilaian difinalkan.');
    }

    /**
     * The submitted answers as `key => [value, note]`, each checked against the rules of its item.
     *
     * @param  array<string, array<string, mixed>>  $items
     * @return array<string, array{value: ?string, note: ?string}>
     */
    private function answers(Request $request, CollateralAppraisal $appraisal, array $items): array
    {
        $answers = [];
        $errors = [];

        foreach ((array) $request->input('answers') as $index => $answer) {
            $key = (string) ($answer['key'] ?? '');
            $item = $items[$key] ?? null;

            if ($item === null) {
                $errors["answers.$index.key"] = 'Isian ini bukan bagian dari penilaian.';

                continue;
            }

            if ($appraisal->loan_application_id !== null && str_starts_with($key, 'h:') && in_array(substr($key, 2), AppraisalSaver::LOCKED_BY_LOAN, true)) {
                $errors["values.$key"] = 'Data ini diambil dari berkas pengajuan.';

                continue;
            }

            $value = is_scalar($answer['value'] ?? null) ? trim((string) $answer['value']) : null;
            $validator = Validator::make(['value' => $value === '' ? null : $value], ['value' => AppraisalRules::for($item)], [], ['value' => mb_strtolower($item['label'])]);

            if ($validator->fails()) {
                $errors["values.$key"] = $validator->errors()->first('value');

                continue;
            }

            $note = trim((string) ($answer['note'] ?? ''));
            $answers[$key] = ['value' => $value === '' ? null : $value, 'note' => $note === '' ? null : $note];
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $answers;
    }
}
