<?php

namespace Tests\Feature;

use App\Models\CommitteePath;
use App\Models\LoanApplication;
use App\Models\LoanSchedule;
use App\Models\Notification;
use App\Models\Office;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Penjadwalan Survei — tahap 2.
 * Menguji penjadwalan, penjadwalan ulang, batas 3 kali, pembatalan oleh staff
 * analis, keutuhan histori, notifikasi, dan penguncian berkas non-DRAFT.
 */
class LoanSchedulingTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function kasi(): User
    {
        return User::role('Kasi Analis')->firstOrFail();
    }

    private function staff(): User
    {
        return User::role('Staff Analis')->firstOrFail();
    }

    /** Berkas siap dijadwalkan (status DIAJUKAN). */
    private function submitted(?User $kasi = null): LoanApplication
    {
        return LoanApplication::create([
            'application_code' => LoanApplication::nextCode(),
            'application_date' => now()->toDateString(),
            'status' => 'DIAJUKAN',
            'nik' => '3213011203950001',
            'full_name' => 'YAYAT SUHAYAT',
            'product_id' => Product::first()->id,
            'committee_path_id' => CommitteePath::first()->id,
            'office_id' => Office::first()->id,
            'supervisor_id' => ($kasi ?? $this->kasi())->id,
            'requested_amount' => 10_000_000,
            'requested_tenor' => 24,
            'created_by' => 'IT Support',
        ]);
    }

    public function test_kasi_analis_can_schedule_survey_and_notify_staff(): void
    {
        $kasi = $this->kasi();
        $staff = $this->staff();
        $record = $this->submitted($kasi);

        $this->actingAs($kasi)
            ->post("/scheduling-simulation/{$record->id}", [
                'survey_date' => now()->addDay()->toDateString(),
                'surveyor_id' => $staff->id,
                'note' => 'Bawa berkas agunan',
            ])
            ->assertSessionHas('success');

        $record->refresh();
        $this->assertSame('PENJADWALAN', $record->status);
        $this->assertSame($staff->id, $record->surveyor_id);
        $this->assertSame(now()->addDay()->toDateString(), $record->survey_date->toDateString());

        $log = $record->schedules()->first();
        $this->assertSame(LoanSchedule::ACTION_SCHEDULE, $log->action);
        $this->assertSame(1, $log->sequence);
        $this->assertSame($staff->name, $log->surveyor_name);
        $this->assertSame($kasi->name, $log->created_by);

        $this->assertTrue(Notification::where('user_id', $staff->id)->exists());
    }

    public function test_daftar_default_hanya_berkas_kasi_yang_login(): void
    {
        $kasi = $this->kasi();
        $other = User::role('Kasi Analis')->where('id', '!=', $kasi->id)->firstOrFail();
        $mine = $this->submitted($kasi);
        $theirs = $this->submitted($other);

        $codes = fn (string $url) => collect($this->get($url)->viewData('page')['props']['records']['data'])
            ->pluck('application_code');

        $this->actingAs($kasi);

        $own = $codes('/scheduling-simulation');
        $this->assertTrue($own->contains($mine->application_code));
        $this->assertFalse($own->contains($theirs->application_code));

        $all = $codes('/scheduling-simulation?scope=semua');
        $this->assertTrue($all->contains($theirs->application_code));
    }

    public function test_pembatalan_staff_analis_meminta_jadwal_ulang_dan_histori_utuh(): void
    {
        $kasi = $this->kasi();
        $staff = $this->staff();
        $record = $this->submitted($kasi);

        $this->actingAs($kasi)->post("/scheduling-simulation/{$record->id}", [
            'survey_date' => now()->addDay()->toDateString(),
            'surveyor_id' => $staff->id,
        ]);

        $this->actingAs($staff)
            ->post("/scheduling-simulation/{$record->id}/cancel", ['reason' => 'Nasabah tidak di tempat'])
            ->assertSessionHas('success');

        $record->refresh();
        $this->assertSame('DIAJUKAN', $record->status);
        $this->assertNull($record->surveyor_id);
        $this->assertNull($record->survey_date);

        // Histori jadwal pertama tidak boleh hilang.
        $this->assertSame(2, $record->schedules()->count());
        $last = $record->schedules()->get()->last();
        $this->assertSame(LoanSchedule::ACTION_CANCEL, $last->action);
        $this->assertSame('Nasabah tidak di tempat', $last->reason);

        // Kasi Analis diberi tahu untuk menjadwalkan ulang.
        $this->assertTrue(Notification::where('user_id', $kasi->id)->exists());
    }

    public function test_penjadwalan_boleh_lebih_dari_tiga_kali_dengan_peringatan(): void
    {
        $kasi = $this->kasi();
        $staff = $this->staff();
        $record = $this->submitted($kasi);

        for ($i = 1; $i <= 3; $i++) {
            $this->actingAs($kasi)->post("/scheduling-simulation/{$record->id}", [
                'survey_date' => now()->addDays($i)->toDateString(),
                'surveyor_id' => $staff->id,
            ])->assertSessionHas('success');

            $this->actingAs($staff)
                ->post("/scheduling-simulation/{$record->id}/cancel", ['reason' => "Batal ke-{$i}"])
                ->assertSessionHas('success');
        }

        $record->refresh();
        $this->assertSame('DIAJUKAN', $record->status);
        $this->assertSame(6, $record->schedules()->count());

        // Sudah 3 kali → masih boleh dijadwalkan, hanya ditandai lewat batas.
        $this->actingAs($kasi)->post("/scheduling-simulation/{$record->id}", [
            'survey_date' => now()->addDays(9)->toDateString(),
            'surveyor_id' => $staff->id,
        ])->assertSessionHas('success');

        $this->assertSame('PENJADWALAN', $record->refresh()->status);
        $this->assertSame(4, $record->schedules()->where('action', '!=', 'BATAL')->count());

        $row = collect($this->get('/scheduling-simulation')->viewData('page')['props']['records']['data'])
            ->firstWhere('application_code', $record->application_code);
        $this->assertTrue($row['over_limit']);
    }

    public function test_berkas_diajukan_tidak_bisa_diubah_lagi(): void
    {
        $record = $this->submitted();
        $user = User::where('username', 'superadmin')->firstOrFail();
        $this->actingAs($user);

        $this->put("/loan-simulation/{$record->id}", [
            'application_date' => now()->toDateString(),
            'product_id' => Product::first()->id,
            'committee_path_id' => CommitteePath::first()->id,
            'requested_amount' => 99_000_000,
            'requested_tenor' => 12,
            'method_id' => 1,
            'installment_id' => 1,
            'interest_rate' => 12,
            'usage_type' => 'KONSUMTIF',
            'office_id' => Office::first()->id,
            'supervisor_id' => $user->id,
        ])->assertSessionHas('error');

        $this->assertSame(10_000_000, $record->refresh()->requested_amount);

        $this->delete("/loan-simulation/{$record->id}")->assertSessionHas('error');
        $this->assertNotNull(LoanApplication::find($record->id));
    }

    public function test_tanggal_survei_tidak_boleh_masa_lalu(): void
    {
        $record = $this->submitted();

        $this->actingAs($this->kasi())
            ->post("/scheduling-simulation/{$record->id}", [
                'survey_date' => now()->subDay()->toDateString(),
                'surveyor_id' => $this->staff()->id,
            ])
            ->assertSessionHasErrors('survey_date');

        $this->assertSame('DIAJUKAN', $record->refresh()->status);
    }
}
