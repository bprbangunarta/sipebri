<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\Branding;
use Illuminate\Database\Seeder;

/** Setelan aplikasi: identitas merek, SEO, kontak, dan preferensi tampilan. */
class SettingSeeder extends Seeder
{
    private const SETTINGS = [
        'app_name' => 'SIPEBRI',
        'app_url' => null,
        'brand_color' => '#0F0F0F',
        'brand_initials' => '</>',
        'canonical_url' => 'https://sipebri.bprbangunarta.co.id',
        'company' => 'PT BPR Bangunarta',
        'date_format' => 'DD/MM/YYYY',
        'favicon' => null,
        'footer_text' => '@ 2026 PT BPR Bangunarta. All Rights Reserved.',
        'language' => 'id',
        'logo_dark' => null,
        'logo_light' => null,
        'meta_description' => 'Sistem untuk mengelola proses pemberian kredit secara terstruktur, efisien, dan terdokumentasi dari pengajuan hingga persetujuan.',
        'meta_keywords' => 'bpr, bangunarta, kredit',
        'meta_title' => 'SIPEBRI: Sistem Pemberian Kredit',
        'og_description' => 'Sistem pemberian kredit PT BPR Bangunarta: pengajuan, survey, analisa, hingga persetujuan komite.',
        'og_image' => null,
        'og_title' => 'SIPEBRI: Sistem Pemberian Kredit',
        'permission_entity_order' => '["permissions","roles","users","appearance","menus","storage","activity","dashboard","profile","offices","institutions","products","installments","methods","committees","collateral-types","binding-types"]',
        'search_indexable' => '0',
        'support_email' => 'info@bprbangunarta.co.id',
        'tagline' => 'Sistem Pemberian Kredit',
        'thumbnail' => null,
        'timezone' => 'Asia/Jakarta',
    ];

    public function run(): void
    {
        foreach (self::SETTINGS as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Cache branding disimpan selamanya — wajib dibuang agar hasil seeding langsung terpakai.
        Branding::forget();
    }
}
