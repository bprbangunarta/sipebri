<?php

namespace Tests\Feature;

use App\Models\CommitteePath;
use App\Models\LoanApplication;
use App\Models\LoanSurvey;
use App\Models\LoanSurveyPhoto;
use App\Models\Office;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Survei — tahap 3. Daftar hanya jadwal HARI INI milik staff analis yang
 * bertugas, foto lokasi + koordinat wajib, dan hasil terkunci setelah disimpan.
 */
class LoanSurveyTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        config(['filesystems.default' => 'public']);
    }

    private function staff(): User
    {
        return User::role('Staff Analis')->firstOrFail();
    }

    private function scheduled(?string $date = null, ?User $surveyor = null): LoanApplication
    {
        return LoanApplication::create([
            'application_code' => LoanApplication::nextCode(),
            'application_date' => now()->toDateString(),
            'status' => 'PENJADWALAN',
            'nik' => '3213011203950001',
            'full_name' => 'YAYAT SUHAYAT',
            'product_id' => Product::first()->id,
            'committee_path_id' => CommitteePath::first()->id,
            'office_id' => Office::first()->id,
            'supervisor_id' => User::role('Kasi Analis')->firstOrFail()->id,
            'surveyor_id' => ($surveyor ?? $this->staff())->id,
            'survey_date' => $date ?? now()->toDateString(),
            'requested_amount' => 10_000_000,
            'requested_tenor' => 24,
            'created_by' => 'IT Support',
        ]);
    }

    private function photo(): UploadedFile
    {
        return UploadedFile::fake()->image('lokasi.jpg', 640, 480);
    }

    public function test_daftar_hanya_jadwal_hari_ini_milik_staff_bertugas(): void
    {
        $staff = $this->staff();
        $other = User::role('Staff Analis')->where('id', '!=', $staff->id)->firstOrFail();

        $todayMine = $this->scheduled();
        $tomorrow = $this->scheduled(now()->addDay()->toDateString());
        $todayOther = $this->scheduled(null, $other);

        $codes = collect($this->actingAs($staff)->get('/survey-simulation')
            ->viewData('page')['props']['records']['data'])->pluck('application_code');

        $this->assertTrue($codes->contains($todayMine->application_code));
        $this->assertFalse($codes->contains($tomorrow->application_code));
        $this->assertFalse($codes->contains($todayOther->application_code));
    }

    public function test_lembar_survei_hanya_untuk_petugas_yang_ditugaskan(): void
    {
        $record = $this->scheduled();
        $other = User::role('Staff Analis')->where('id', '!=', $record->surveyor_id)->firstOrFail();

        $this->actingAs($other)->get("/survey-simulation/{$record->id}")->assertNotFound();
        $this->actingAs($this->staff())->get("/survey-simulation/{$record->id}")->assertOk();
    }

    public function test_foto_wajib_beserta_koordinat_lalu_hasil_terkunci(): void
    {
        $staff = $this->staff();
        $record = $this->scheduled();
        $this->actingAs($staff);

        // Tanpa foto → ditolak.
        $this->post("/survey-simulation/{$record->id}", ['note' => 'Lokasi sesuai'])
            ->assertSessionHas('error');
        $this->assertSame('PENJADWALAN', $record->refresh()->status);

        // Koordinat wajib.
        $this->post("/survey-simulation/{$record->id}/photos", ['photo' => $this->photo()])
            ->assertSessionHasErrors(['latitude', 'longitude']);

        $this->post("/survey-simulation/{$record->id}/photos", [
            'photo' => $this->photo(),
            'latitude' => -6.5712345,
            'longitude' => 107.7601234,
            'source' => 'KAMERA',
        ])->assertSessionHas('success');

        $photo = LoanSurveyPhoto::where('loan_application_id', $record->id)->firstOrFail();
        $this->assertNull($photo->loan_survey_id);
        Storage::disk('public')->assertExists(str_replace('local:', '', $photo->path));

        $this->post("/survey-simulation/{$record->id}", ['note' => 'Lokasi sesuai, usaha aktif'])
            ->assertSessionHas('success');

        $record->refresh();
        $this->assertSame('SURVEY', $record->status);

        $survey = LoanSurvey::where('loan_application_id', $record->id)->firstOrFail();
        $this->assertSame('Lokasi sesuai, usaha aktif', $survey->note);
        $this->assertSame($staff->name, $survey->created_by);
        $this->assertSame('-6.5712345', (string) $survey->latitude);
        $this->assertSame($survey->id, $photo->refresh()->loan_survey_id);

        // Terkunci: tidak bisa tambah foto, hapus foto, atau simpan ulang.
        $this->post("/survey-simulation/{$record->id}/photos", [
            'photo' => $this->photo(), 'latitude' => -6.5, 'longitude' => 107.7,
        ])->assertSessionHas('error');
        $this->delete("/survey-simulation/{$record->id}/photos/{$photo->id}")->assertSessionHas('error');
        $this->post("/survey-simulation/{$record->id}")->assertSessionHas('error');
        $this->assertSame(1, LoanSurveyPhoto::where('loan_application_id', $record->id)->count());
    }

    public function test_batas_lima_foto(): void
    {
        $record = $this->scheduled();
        $this->actingAs($this->staff());

        for ($i = 0; $i < LoanSurveyPhoto::MAX_PHOTOS; $i++) {
            $this->post("/survey-simulation/{$record->id}/photos", [
                'photo' => $this->photo(), 'latitude' => -6.5, 'longitude' => 107.7,
            ])->assertSessionHas('success');
        }

        $this->post("/survey-simulation/{$record->id}/photos", [
            'photo' => $this->photo(), 'latitude' => -6.5, 'longitude' => 107.7,
        ])->assertSessionHas('error');

        $this->assertSame(
            LoanSurveyPhoto::MAX_PHOTOS,
            LoanSurveyPhoto::where('loan_application_id', $record->id)->count(),
        );
    }

    public function test_staff_analis_bisa_hapus_foto_sebelum_disimpan(): void
    {
        $record = $this->scheduled();
        $this->actingAs($this->staff());

        $this->post("/survey-simulation/{$record->id}/photos", [
            'photo' => $this->photo(), 'latitude' => -6.5, 'longitude' => 107.7,
        ]);

        $photo = LoanSurveyPhoto::where('loan_application_id', $record->id)->firstOrFail();
        $this->delete("/survey-simulation/{$record->id}/photos/{$photo->id}")->assertSessionHas('success');

        $this->assertSame(0, LoanSurveyPhoto::where('loan_application_id', $record->id)->count());
        Storage::disk('public')->assertMissing(str_replace('local:', '', $photo->path));
    }
}
