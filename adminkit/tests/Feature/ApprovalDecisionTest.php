<?php

namespace Tests\Feature;

use App\Models\AnalysisMemorandum;
use App\Models\CommitteePath;
use App\Models\LoanApplication;
use App\Models\LoanApproval;
use App\Models\Method;
use App\Models\Office;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Persetujuan Komite: keputusan berjenjang mengikuti jalur komite produk. */
class ApprovalDecisionTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function committeeFile(int $amount = 50_000_000): LoanApplication
    {
        $product = Product::where('alias', 'KRU')->firstOrFail();
        $path = CommitteePath::where('product_id', $product->id)->whereNull('condition')->firstOrFail();

        $app = LoanApplication::create([
            'application_code' => LoanApplication::nextCode(),
            'application_date' => now()->toDateString(),
            'status' => 'KOMITE',
            'nik' => '3213011203950001',
            'full_name' => 'YAYAT SUHAYAT',
            'product_id' => $product->id,
            'committee_path_id' => $path->id,
            'office_id' => Office::first()->id,
            'method_id' => Method::first()->id,
            'supervisor_id' => User::role('Kasi Analis')->firstOrFail()->id,
            'requested_amount' => $amount,
            'requested_tenor' => 24,
            'interest_rate' => 24,
            'analysis_submitted_at' => now(),
            'analysis_submitted_by' => 'Staff Analis',
            'created_by' => 'IT Support',
        ]);

        AnalysisMemorandum::create([
            'loan_application_id' => $app->id,
            'usulan_plafond' => $amount,
            'jangka_waktu' => 24,
            's_bunga' => 24,
            'b_provisi' => 1,
            'b_admin' => 4,
        ]);

        return $app;
    }

    public function test_kasi_can_only_escalate_when_amount_exceeds_its_ceiling(): void
    {
        $app = $this->committeeFile(50_000_000);
        $kasi = User::role('Kasi Analis')->firstOrFail();

        $page = $this->actingAs($kasi)->get("/approval-simulation/{$app->id}");
        $page->assertOk();

        $flow = $page->viewData('page')['props']['flow'];
        $this->assertSame(1, $flow['pending_level']);
        $this->assertTrue($flow['my_turn']);
        $this->assertSame(['TERUSKAN', 'DITOLAK', 'DIBATALKAN'], $flow['allowed']);

        $this->actingAs($kasi)->post("/approval-simulation/{$app->id}/decision", [
            'decision' => 'DISETUJUI',
            'method_id' => Method::first()->id,
            'amount' => 50_000_000,
            'tenor' => 24,
            'interest_rate' => 24,
            'provision_rate' => 1,
            'admin_rate' => 4,
        ])->assertSessionHasErrors('decision');

        $this->assertSame('KOMITE', $app->fresh()->status);
    }

    public function test_escalation_moves_the_pending_level_and_next_tier_decides(): void
    {
        $app = $this->committeeFile(50_000_000);
        $method = Method::first()->id;

        $this->actingAs(User::role('Kasi Analis')->firstOrFail())
            ->post("/approval-simulation/{$app->id}/decision", [
                'decision' => 'TERUSKAN',
                'method_id' => $method,
                'amount' => 50_000_000,
                'tenor' => 24,
                'interest_rate' => 24,
                'provision_rate' => 1,
                'admin_rate' => 4,
                'note' => 'Layak, naik komite I.',
            ])->assertRedirect('/approval-simulation');

        $this->assertSame('KOMITE', $app->fresh()->status);
        $this->assertSame('TERUSKAN', LoanApproval::where('level', 1)->firstOrFail()->decision);

        $kabag = User::role('Kabag Analis')->firstOrFail();
        $flow = $this->actingAs($kabag)->get("/approval-simulation/{$app->id}")->viewData('page')['props']['flow'];

        $this->assertSame(2, $flow['pending_level']);
        $this->assertTrue($flow['my_turn']);
        $this->assertContains('DISETUJUI', $flow['allowed']);

        $this->actingAs($kabag)->post("/approval-simulation/{$app->id}/decision", [
            'decision' => 'DISETUJUI',
            'method_id' => $method,
            'amount' => 45_000_000,
            'tenor' => 24,
            'interest_rate' => 24,
            'provision_rate' => 1,
            'admin_rate' => 4,
            'note' => 'Disetujui dengan plafon turun.',
        ])->assertRedirect('/approval-simulation');

        $app->refresh();
        $this->assertSame('DISETUJUI', $app->status);
        $this->assertSame(45_000_000, (int) $app->approved_amount);
        $this->assertSame(24, (int) $app->approved_tenor);
        $this->assertNotNull($app->decided_at);
    }

    public function test_other_tier_cannot_decide_out_of_turn(): void
    {
        $app = $this->committeeFile(50_000_000);
        $direktur = User::role('Direktur Utama')->firstOrFail();

        $flow = $this->actingAs($direktur)->get("/approval-simulation/{$app->id}")->viewData('page')['props']['flow'];
        $this->assertFalse($flow['my_turn']);
        $this->assertSame([], $flow['allowed']);

        $this->actingAs($direktur)->post("/approval-simulation/{$app->id}/decision", [
            'decision' => 'DISETUJUI',
            'method_id' => Method::first()->id,
            'amount' => 50_000_000,
            'tenor' => 24,
            'interest_rate' => 24,
            'provision_rate' => 1,
            'admin_rate' => 4,
        ])->assertForbidden();
    }

    public function test_rejection_closes_the_file(): void
    {
        $app = $this->committeeFile(20_000_000);

        $this->actingAs(User::role('Kasi Analis')->firstOrFail())
            ->post("/approval-simulation/{$app->id}/decision", [
                'decision' => 'DITOLAK',
                'method_id' => Method::first()->id,
                'amount' => 20_000_000,
                'tenor' => 24,
                'interest_rate' => 24,
                'provision_rate' => 1,
                'admin_rate' => 4,
                'note' => 'Kapasitas tidak memadai.',
            ])->assertRedirect('/approval-simulation');

        $app->refresh();
        $this->assertSame('DITOLAK', $app->status);
        $this->assertSame(0, (int) $app->approved_amount);
        $this->assertSame('Kapasitas tidak memadai.', $app->decision_note);
    }
}
