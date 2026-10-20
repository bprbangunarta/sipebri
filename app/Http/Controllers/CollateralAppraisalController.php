<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Models\AppraisalPhoto;
use App\Models\Collateral;
use App\Models\CollateralAppraisal;
use App\Models\LoanApplication;
use App\Support\Appraisal\AppraisalGuard;
use App\Support\Appraisal\AppraisalPayload;
use App\Support\Appraisal\AppraisalStarter;
use App\Support\Appraisal\AppraisalTemplates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

/**
 * The appraisals of a collateral: the history of them, starting a new one, opening one, and removing a draft.
 */
class CollateralAppraisalController extends Controller
{
    public function index(Request $request, Collateral $collateral): Response
    {
        $appraisals = $collateral->appraisals()->get();

        return Inertia::render('collaterals/appraisals/index', [
            'collateral' => AppraisalPayload::collateral($collateral),
            'appraisals' => $appraisals->map(fn (CollateralAppraisal $appraisal): array => AppraisalPayload::summary($appraisal))->values(),
            'purposes' => CollateralAppraisal::PURPOSES,
            'loans' => $collateral->loanApplications()->orderByDesc('loan_applications.id')->get(['loan_applications.id', 'application_code', 'full_name', 'requested_amount'])
                ->map(fn (LoanApplication $loan): array => ['value' => (string) $loan->id, 'label' => "{$loan->application_code} : {$loan->full_name}"])->values(),
            'hasDraft' => $appraisals->contains(fn (CollateralAppraisal $appraisal): bool => ! $appraisal->isFinal()),
            'template' => AppraisalTemplates::for($collateral)->key(),
            'canManage' => $request->user()->can('collaterals.manage'),
        ]);
    }

    public function store(Request $request, Collateral $collateral): RedirectResponse
    {
        $data = $request->validate([
            'purpose' => ['required', Rule::in(array_keys(CollateralAppraisal::PURPOSES))],
            'loan_application_id' => ['nullable', 'integer'],
            'copy_from' => ['nullable', 'integer'],
        ], [], ['purpose' => 'tujuan penilaian', 'loan_application_id' => 'berkas pengajuan', 'copy_from' => 'penilaian sebelumnya']);

        $loan = isset($data['loan_application_id']) ? $collateral->loanApplications()->whereKey((int) $data['loan_application_id'])->first() : null;
        $from = isset($data['copy_from']) ? $collateral->appraisals()->whereKey((int) $data['copy_from'])->first() : null;

        foreach (['loan_application_id' => [$data['loan_application_id'] ?? null, $loan], 'copy_from' => [$data['copy_from'] ?? null, $from]] as $field => [$given, $found]) {
            if ($given !== null && $found === null) {
                throw ValidationException::withMessages([$field => 'Pilihan ini bukan milik jaminan tersebut.']);
            }
        }

        try {
            $appraisal = AppraisalStarter::start($collateral, $request->user(), $data['purpose'], $loan, $from);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if ($from !== null) {
            Audit::record('collateral_appraisals.copied', 'collateral_appraisals', 'copied', $appraisal, new: ['copied_from' => $from->id], context: ['sections_left_out' => AppraisalStarter::NOT_COPIED]);
        }

        return to_route('appraisals.show', $appraisal)->with('success', "Penilaian #{$appraisal->sequence} dimulai.");
    }

    public function show(Request $request, CollateralAppraisal $collateralAppraisal): Response
    {
        Audit::record('collateral_appraisals.viewed', 'collateral_appraisals', 'viewed', $collateralAppraisal);

        return Inertia::render('collaterals/appraisals/show', [
            ...AppraisalPayload::show($collateralAppraisal, AppraisalTemplates::of($collateralAppraisal), $request->user()->can('collaterals.manage')),
            'section' => $request->query('section'),
        ]);
    }

    public function destroy(CollateralAppraisal $collateralAppraisal): RedirectResponse
    {
        if ($refused = AppraisalGuard::refuseIfFinal($collateralAppraisal)) {
            return $refused;
        }

        $collateralAppraisal->photos->each(fn (AppraisalPhoto $photo) => AppraisalPhoto::disk()->delete($photo->path));
        $collateral = $collateralAppraisal->collateral;
        $collateralAppraisal->delete();

        return to_route('collaterals.appraisals.index', $collateral)->with('success', 'Draf penilaian dihapus.');
    }
}
