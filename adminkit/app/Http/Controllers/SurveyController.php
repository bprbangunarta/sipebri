<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\LoanApplication;
use App\Models\LoanSurvey;
use App\Models\LoanSurveyPhoto;
use App\Support\CustomerDirectory;
use App\Support\FileStorage;
use App\Support\Notify;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Survei — tahap 3 dari alur kredit.
 * Menampilkan berkas berstatus PENJADWALAN milik staff analis yang bertugas
 * dengan tanggal survei HARI INI. Wajib foto lokasi + koordinat saat difoto.
 */
class SurveyController extends Controller
{
    private const LABEL = 'Survei Kredit';

    public function index(Request $request): Response
    {
        $search = TableQuery::search($request);
        $sort = TableQuery::sort($request, ['application_code', 'full_name', 'survey_date'], 'application_code');
        $dir = TableQuery::direction($request);

        $records = LoanApplication::query()
            ->with(['product:id,alias,name', 'supervisor:id,name'])
            ->where('status', 'PENJADWALAN')
            ->where('surveyor_id', $request->user()->id)
            ->whereDate('survey_date', now()->toDateString())
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                foreach (['application_code', 'full_name', 'nik', 'requested_amount'] as $col) {
                    $w->orWhere($col, 'like', "%{$search}%");
                }

                $w->orWhereHas('product', fn ($p) => $p
                    ->where('alias', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%"));
            }))
            ->orderBy($sort, $dir)
            ->paginate(TableQuery::perPage($request))
            ->withQueryString();

        return Inertia::render('SurveySimulation', [
            'records' => [
                'data' => collect($records->items())->map(fn (LoanApplication $r) => [
                    'id' => $r->id,
                    'application_code' => $r->application_code,
                    'full_name' => $r->full_name,
                    'nik' => $r->nik,
                    'product_label' => $r->product ? "{$r->product->alias} : {$r->product->name}" : null,
                    'supervisor_name' => $r->supervisor?->name,
                    'requested_amount' => $r->requested_amount,
                    'requested_tenor' => $r->requested_tenor,
                    'survey_date_label' => $r->survey_date?->translatedFormat('d M Y'),
                ])->all(),
                'meta' => TableQuery::meta($records),
            ],
            'filters' => ['search' => $search, 'sort' => $sort, 'dir' => $dir],
            'today' => now()->translatedFormat('d M Y'),
        ]);
    }

    /** Lembar kerja survei; hanya untuk staff analis yang ditugaskan. */
    public function show(Request $request, LoanApplication $loanApplication): Response
    {
        $this->authorizeSurveyor($request, $loanApplication);

        $survey = LoanSurvey::where('loan_application_id', $loanApplication->id)->latest('id')->first();

        return Inertia::render('SurveyDetail', [
            'record' => [
                'id' => $loanApplication->id,
                'application_code' => $loanApplication->application_code,
                'application_date' => $loanApplication->application_date?->translatedFormat('d M Y'),
                'status' => $loanApplication->status,
                'full_name' => $loanApplication->full_name,
                'nik' => $loanApplication->nik,
                'cif_number' => $loanApplication->cif_number,
                'product_label' => $loanApplication->product
                    ? "{$loanApplication->product->alias} : {$loanApplication->product->name}"
                    : null,
                'usage_type' => $loanApplication->usage_type,
                'requested_amount' => $loanApplication->requested_amount,
                'requested_tenor' => $loanApplication->requested_tenor,
                'supervisor_name' => $loanApplication->supervisor?->name,
                'survey_date_label' => $loanApplication->survey_date?->translatedFormat('d M Y'),
                'schedule_note' => $loanApplication->schedules()->get()->last()?->note,
                'locked' => $loanApplication->status !== 'PENJADWALAN',
            ],
            'customer' => $this->customer($loanApplication),
            'collaterals' => $loanApplication->collaterals()->get()->map(fn ($c) => [
                'id' => $c->id,
                'collateral_id' => $c->collateral_id,
                'owner_name' => $c->owner_name,
                'description' => $c->description,
                'appraisal_value' => $c->appraisal_value,
            ])->all(),
            'photos' => $this->photos($loanApplication),
            'survey' => $survey ? [
                'note' => $survey->note,
                'latitude' => $survey->latitude,
                'longitude' => $survey->longitude,
                'created_by' => $survey->created_by,
                'created_at' => $survey->created_at?->translatedFormat('d M Y H:i'),
            ] : null,
            'maxPhotos' => LoanSurveyPhoto::MAX_PHOTOS,
        ]);
    }

    /** Unggah satu foto lokasi beserta koordinat saat difoto. */
    public function storePhoto(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeSurveyor($request, $loanApplication);

        if ($loanApplication->status !== 'PENJADWALAN') {
            return back()->with('error', 'Hasil survei sudah dikunci.');
        }

        $count = LoanSurveyPhoto::where('loan_application_id', $loanApplication->id)
            ->whereNull('loan_survey_id')->count();

        if ($count >= LoanSurveyPhoto::MAX_PHOTOS) {
            return back()->with('error', 'Maksimal '.LoanSurveyPhoto::MAX_PHOTOS.' foto per survei.');
        }

        $data = $request->validate([
            'photo' => ['required', 'image', 'max:8192'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'source' => ['nullable', 'in:KAMERA,GALERI'],
        ], [], [
            'photo' => 'foto',
            'latitude' => 'lintang',
            'longitude' => 'bujur',
        ]);

        LoanSurveyPhoto::create([
            'loan_application_id' => $loanApplication->id,
            'path' => FileStorage::store($request->file('photo'), 'survei/'.$loanApplication->application_code),
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'source' => $data['source'] ?? 'KAMERA',
            'created_by' => $request->user()->name,
        ]);

        return back()->with('success', 'Foto lokasi tersimpan.');
    }

    public function destroyPhoto(Request $request, LoanApplication $loanApplication, LoanSurveyPhoto $photo): RedirectResponse
    {
        $this->authorizeSurveyor($request, $loanApplication);

        if ($loanApplication->status !== 'PENJADWALAN' || $photo->loan_survey_id) {
            return back()->with('error', 'Foto pada survei yang sudah disimpan tidak dapat dihapus.');
        }

        FileStorage::delete($photo->path);
        $photo->delete();

        return back()->with('success', 'Foto dihapus.');
    }

    /** Menyimpan hasil survei: berkas menjadi SURVEY dan terkunci. */
    public function store(Request $request, LoanApplication $loanApplication): RedirectResponse
    {
        $this->authorizeSurveyor($request, $loanApplication);

        if ($loanApplication->status !== 'PENJADWALAN') {
            return back()->with('error', 'Hasil survei sudah dikunci.');
        }

        $photos = LoanSurveyPhoto::where('loan_application_id', $loanApplication->id)
            ->whereNull('loan_survey_id')->orderBy('id')->get();

        if ($photos->isEmpty()) {
            return back()->with('error', 'Unggah minimal satu foto lokasi sebelum menyimpan.');
        }

        $data = $request->validate(
            ['note' => ['nullable', 'string', 'max:500']],
            [],
            ['note' => 'catatan hasil survei'],
        );

        $survey = LoanSurvey::create([
            'loan_application_id' => $loanApplication->id,
            'sequence' => LoanSurvey::where('loan_application_id', $loanApplication->id)->count() + 1,
            'surveyor_id' => $request->user()->id,
            'surveyor_name' => $request->user()->name,
            'survey_date' => $loanApplication->survey_date,
            'note' => $data['note'] ?? null,
            'latitude' => $photos->first()->latitude,
            'longitude' => $photos->first()->longitude,
            'created_by' => $request->user()->name,
        ]);

        LoanSurveyPhoto::whereIn('id', $photos->pluck('id'))->update(['loan_survey_id' => $survey->id]);

        $loanApplication->update(['status' => 'SURVEY']);

        ActivityLog::record(
            "Menyimpan hasil survei berkas {$loanApplication->application_code}",
            self::LABEL,
            'success',
            $loanApplication,
        );

        Notify::toUser(
            $request->user(),
            'Berkas siap dianalisa',
            self::LABEL,
            "Survei berkas {$loanApplication->application_code} selesai, lanjutkan ke tahap analisa.",
            '/analysis-simulation',
        );

        return back()->with('success', 'Hasil survei disimpan. Berkas lanjut ke tahap analisa.');
    }

    /** Hanya staff analis yang ditugaskan boleh membuka lembar survei. */
    private function authorizeSurveyor(Request $request, LoanApplication $r): void
    {
        if ($r->surveyor_id !== $request->user()->id) {
            throw new NotFoundHttpException('Berkas ini bukan penugasan Anda.');
        }
    }

    private function photos(LoanApplication $r): array
    {
        return LoanSurveyPhoto::where('loan_application_id', $r->id)
            ->orderBy('id')
            ->get()
            ->map(fn (LoanSurveyPhoto $p) => [
                'id' => $p->id,
                'url' => $p->url,
                'latitude' => (float) $p->latitude,
                'longitude' => (float) $p->longitude,
                'source' => $p->source,
                'saved' => (bool) $p->loan_survey_id,
                'created_at' => $p->created_at?->translatedFormat('d M Y H:i'),
            ])->all();
    }

    /** Identitas pemohon dari Codex (ringkas, tidak disimpan di SIPEBRI). */
    private function customer(LoanApplication $r): ?array
    {
        try {
            $customer = CustomerDirectory::find((string) $r->nik);
        } catch (Throwable $e) {
            report($e);

            return null;
        }

        return $customer ? [
            'full_name' => $customer['full_name'],
            'address' => $customer['address'],
            'region_label' => $customer['region_label'],
            'phone' => $customer['phone'],
            'employer_name' => $customer['employer_name'],
        ] : null;
    }
}
