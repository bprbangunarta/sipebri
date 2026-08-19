<?php

namespace Database\Seeders;

use App\Models\CommitteePath;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Aturan komite kredit bawaan sesuai dokumentasi kebijakan:
 *  - Jalur PLAFON: 14 produk umum + KBT (PERPADIAN).
 *  - Jalur HIERARKI: KUP, KKO, KBT (PERLELEAN), dan kategori RELOAN (lintas produk).
 * Jenjang berisi HANYA level pemutus. Hak mengajukan/meneruskan berkas ke komite
 * bukan jenjang komite, melainkan izin pada modul pengajuan kredit.
 * Idempoten: jalur dikenali dari kombinasi produk + kondisi; jenjang hanya diisi
 * saat jalur belum memiliki jenjang, sehingga penyesuaian manual tidak tertimpa.
 */
class CommitteeSeeder extends Seeder
{
    /** Produk yang memakai kewenangan plafon (alias produk). */
    private const PLAFON_PRODUCTS = [
        'KRU', 'KRM', 'PRK', 'KTO', 'KPS', 'KIH', 'KPJ', 'KRS',
        'KPN', 'KIU', 'KTA', 'KPM', 'KPP', 'KRISPI',
    ];

    /** Produk yang memakai hierarki komite tanpa batas plafon. */
    private const HIERARKI_PRODUCTS = ['KUP', 'KKO'];

    /** [label, peranan, min, max, naik, setuju, batal, tolak] */
    private const PLAFON_TIERS = [
        ['Kasi', 'Kasi Analis', 1000, 35_000_000, true, true, true, true],
        ['Komite I', 'Kabag Analis', 35_000_001, 100_000_000, true, true, true, true],
        ['Komite II', 'Direktur Bisnis', 100_000_001, 300_000_000, true, true, true, true],
        ['Komite III', 'Direktur Utama', 300_000_001, null, false, true, true, true],
    ];

    private const HIERARKI_TIERS = [
        ['Kasi', 'Kasi Analis', true, false, false, false],
        ['Komite I', 'Kabag Analis', true, false, false, false],
        ['Komite II', 'Direktur Bisnis', true, false, false, false],
        ['Komite III', 'Direktur Utama', false, true, true, true],
    ];

    public function run(): void
    {
        foreach (self::PLAFON_PRODUCTS as $alias) {
            $this->path($alias, null, 'plafon', 'Kewenangan berdasarkan plafon (jalur umum).');
        }

        $this->path('KBT', 'PERPADIAN', 'plafon', 'Kredit Budidaya Tani sebenarnya.');

        foreach (self::HIERARKI_PRODUCTS as $alias) {
            $this->path($alias, null, 'hierarki', 'Produk kredit khusus karyawan.');
        }

        $this->path('KBT', 'PERLELEAN', 'hierarki', 'Nasabah bermitra dengan perusahaan.');
        $this->path(null, 'RELOAN', 'hierarki', 'Berlaku lintas produk dan mengesampingkan jalur plafon.');
    }

    private function path(?string $alias, ?string $condition, string $mechanism, string $note): void
    {
        $productId = $alias ? Product::where('alias', $alias)->value('id') : null;

        if ($alias && ! $productId) {
            return;
        }

        $path = CommitteePath::firstOrNew(['product_id' => $productId, 'condition' => $condition]);
        $path->fill(['mechanism' => $mechanism, 'note' => $note, 'is_active' => true])->save();

        if ($path->tiers()->exists()) {
            return;
        }

        $mechanism === 'plafon' ? $this->plafonTiers($path) : $this->hierarkiTiers($path);
    }

    private function plafonTiers(CommitteePath $path): void
    {
        foreach (self::PLAFON_TIERS as $i => [$label, $role, $min, $max, $naik, $setuju, $batal, $tolak]) {
            $path->tiers()->create([
                'sort' => $i + 1,
                'label' => $label,
                'role' => $role,
                'min_amount' => $min,
                'max_amount' => $max,
                'can_escalate' => $naik,
                'can_approve' => $setuju,
                'can_cancel' => $batal,
                'can_reject' => $tolak,
            ]);
        }
    }

    private function hierarkiTiers(CommitteePath $path): void
    {
        foreach (self::HIERARKI_TIERS as $i => [$label, $role, $naik, $setuju, $batal, $tolak]) {
            $path->tiers()->create([
                'sort' => $i + 1,
                'label' => $label,
                'role' => $role,
                'can_escalate' => $naik,
                'can_approve' => $setuju,
                'can_cancel' => $batal,
                'can_reject' => $tolak,
            ]);
        }
    }
}
