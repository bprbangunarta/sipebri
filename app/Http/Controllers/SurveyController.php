<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\LoanSurvey;
use App\Models\LoanSurveyPhoto;
use App\Support\CustomerDirectory;
use App\Support\LocationTargets;
use App\Support\Notify;
use App\Support\PhotoExif;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Survey, stage 3 of the credit flow. Lists the files scheduled TODAY for the assigned surveyor.
 * Location photos are mandatory and carry the coordinates captured when the photo was taken.
 */
class SurveyController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->validate(['search' => ['nullable', 'string', 'max:100']])['search'] ?? '';

        $loans = LoanApplication::query()
            ->with(['product:id,alias,name', 'supervisor:id,name'])
            ->where('status', LoanStatus::Scheduling)
            ->where('surveyor_id', $request->user()->id)
            ->whereDate('survey_date', now()->toDateString())
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $q->where(fn ($w) => $w->where('application_code', 'like', $like)->orWhere('full_name', 'like', $like)->orWhere('nik', 'like', $like));
            })
            ->orderBy('application_code')
            ->get();

        return Inertia::render('surveys/index', [
            'loans' => $loans->map(fn (LoanApplication $l): array => [
                ...$l->only(['id', 'application_code', 'full_name', 'nik', 'requested_amount', 'requested_tenor']),
                'product_label' => $l->product ? "{$l->product->alias} : {$l->product->name}" : null,
                'supervisor_name' => $l->supervisor?->name,
            ]),
            'filters' => ['search' => $search],
            'today' => now()->toDateString(),
        ]);
    }

    /** Survey worksheet; only for the assigned surveyor. */
    public function show(Request $request, LoanApplication $loanApplication): Response
    {
        $this->authorizeSurveyor($request, $loanApplication);
        $loan = $loanApplication->load(['product:id,alias,name', 'supervisor:id,name', 'collaterals']);
        Audit::record('surveys.viewed', 'surveys', 'viewed', $loanApplication);
        $survey = $loan->surveys()->reorder('id', 'desc')->first();

        return Inertia::render('surveys/show', [
            'loan' => [
                ...$loan->only(['id', 'application_code', 'full_name', 'nik', 'cif_number', 'usage_type', 'requested_amount', 'requested_tenor']),
                'status' => $loan->status->value,
                'application_date' => $loan->application_date->toDateString(),
                'survey_date' => $loan->survey_date?->toDateString(),
                'product_label' => $loan->product ? "{$loan->product->alias} : {$loan->product->name}" : null,
                'supervisor_name' => $loan->supervisor?->name,
                'schedule_note' => $loan->schedules()->reorder('id', 'desc')->value('note'),
                'locked' => $loan->status !== LoanStatus::Scheduling,
            ],
            'customer' => $this->customer($loan),
            'locations' => LocationTargets::for($loan, $this->customer($loan)['address'] ?? null),
            'photos' => $loan->photos->map(fn (LoanSurveyPhoto $p): array => [
                'id' => $p->id, 'url' => $p->url(), 'collateral_id' => $p->collateral_id, 'latitude' => $p->latitude === null ? null : (float) $p->latitude,
                'longitude' => $p->longitude === null ? null : (float) $p->longitude, 'taken_at' => $p->taken_at?->format('d M Y H:i'),
                'saved' => $p->loan_survey_id !== null, 'created_at' => $p->created_at?->format('d M Y H:i'),
            ]),
            'survey' => $survey ? [
                'note' => $survey->note, 'latitude' => $survey->latitude, 'longitude' => $survey->longitude,
                'created_by' => $survey->created_by, 'created_at' => $survey->created_at?->format('d M Y H:i'),
            ] : null,
            'maxPhotos' => (int) config('credit.max_survey_photos'),
        ]);
    }

    /**
     * Upload one survey photo. Its coordinates and time are kept only when the file still carries them (chat apps strip
     * them); the position of a target is set separately, see SurveyLocationController.
     */
    public function storePhoto(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeSurveyor($request, $loanApplication);

        if ($loanApplication->status !== LoanStatus::Scheduling) {
            return back()->with('error', 'The survey result is locked.');
        }

        $data = $request->validate([
            'photo' => ['required', 'image', 'max:8192'],
            'collateral_id' => ['nullable', 'integer'],
        ], [], ['photo' => 'photo']);

        // A photo belongs to the survey location (no collateral) or to one collateral of the file.
        $collateralId = isset($data['collateral_id']) ? $loanApplication->collaterals()->whereKey((int) $data['collateral_id'])->firstOrFail()->id : null;
        $max = (int) config('credit.max_survey_photos');

        if ($loanApplication->photos()->whereNull('loan_survey_id')->where('collateral_id', $collateralId)->count() >= $max) {
            return back()->with('error', "At most {$max} photos for each place.");
        }

        $file = $request->file('photo');
        $exif = PhotoExif::read($file->getRealPath());

        $loanApplication->photos()->create([
            'collateral_id' => $collateralId,
            'path' => LoanSurveyPhoto::disk()->putFile('surveys/'.$loanApplication->application_code, $file),
            'latitude' => $exif['latitude'],
            'longitude' => $exif['longitude'],
            'taken_at' => $exif['taken_at'],
            'source' => 'upload',
            'created_by' => $request->user()->name,
        ]);

        return back()->with('success', 'Photo saved.');
    }

    public function destroyPhoto(Request $request, LoanApplication $loanApplication, LoanSurveyPhoto $photo): RedirectResponse
    {
        $this->authorizeSurveyor($request, $loanApplication);

        if ($loanApplication->status !== LoanStatus::Scheduling || $photo->loan_survey_id !== null) {
            return back()->with('error', 'Photos of a saved survey cannot be deleted.');
        }

        LoanSurveyPhoto::disk()->delete($photo->path);
        $photo->delete();

        return back()->with('success', 'Photo deleted.');
    }

    /** Save the survey result: the file becomes "survey" and is locked. */
    public function store(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeSurveyor($request, $loanApplication);

        if ($loanApplication->status !== LoanStatus::Scheduling) {
            return back()->with('error', 'The survey result is locked.');
        }

        $photos = $loanApplication->photos()->whereNull('loan_survey_id')->get();

        // The survey location is mandatory: its position and at least one photo. Collateral positions and photos are optional.
        if ($loanApplication->survey_latitude === null) {
            return back()->with('error', 'Mark the survey location before saving.');
        }

        if ($photos->whereNull('collateral_id')->isEmpty()) {
            return back()->with('error', 'Upload at least one photo of the survey location before saving.');
        }

        $data = $request->validate(['note' => ['nullable', 'string', 'max:500']], [], ['note' => 'survey note']);

        $survey = LoanSurvey::create([
            'loan_application_id' => $loanApplication->id,
            'sequence' => $loanApplication->surveys()->count() + 1,
            'surveyor_id' => $request->user()->id,
            'surveyor_name' => $request->user()->name,
            'survey_date' => $loanApplication->survey_date,
            'note' => $data['note'] ?? null,
            'latitude' => $loanApplication->survey_latitude,
            'longitude' => $loanApplication->survey_longitude,
            'location_source' => $loanApplication->survey_source,
            'created_by' => $request->user()->name,
        ]);

        LoanSurveyPhoto::query()->whereIn('id', $photos->modelKeys())->update(['loan_survey_id' => $survey->id]);
        // The bulk update above raises no model events, so the link between the photos and the survey is recorded here.
        Audit::record('surveys.photos_linked', 'surveys', 'photos_linked', $survey, new: ['photo_ids' => $photos->modelKeys()], context: ['loan_application_id' => $loanApplication->id]);
        // The position now lives on the survey record; the file's working copy is cleared for a possible re-survey.
        $loanApplication->update([
            'status' => LoanStatus::Survey,
            'survey_latitude' => null, 'survey_longitude' => null, 'survey_source' => null, 'survey_located_at' => null, 'survey_located_by' => null,
        ]);

        Notify::toUser($request->user(), 'File ready for analysis', 'Survey', "Survey of file {$loanApplication->application_code} is done; it now appears under Analysis.", '/analysis');

        return back()->with('success', 'Survey saved. The file is now ready for analysis.');
    }

    /** Only the assigned surveyor may open the worksheet. */
    private function authorizeSurveyor(Request $request, LoanApplication $loan): void
    {
        abort_unless($loan->surveyor_id === $request->user()->id, 403, 'This file is not assigned to you.');
    }

    /**
     * Short applicant summary from the customer master (not stored here).
     *
     * @return array<string, mixed>|null
     */
    private function customer(LoanApplication $loan): ?array
    {
        try {
            $customer = CustomerDirectory::find($loan->nik);
        } catch (Throwable $e) {
            report($e);

            return null;
        }

        return $customer ? array_intersect_key($customer, array_flip(['full_name', 'address', 'phone', 'employer_name', 'source'])) : null;
    }
}
