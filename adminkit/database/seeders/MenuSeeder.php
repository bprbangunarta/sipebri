<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

/**
 * Menu sidebar bawaan (maks 3 tingkat).
 * Format: [label, href, icon, permission, anak-anak].
 * Idempoten: item dikenali dari kombinasi area + induk + label.
 */
class MenuSeeder extends Seeder
{
    private const MENUS = [
        'member' => [
            ['Dashboard', '/', 'chart-pie', 'dashboard.view', []],
            ['Referensi', null, 'library-big', null, [
                ['Data Kantor', '/offices', 'building', 'offices.view', []],
                ['Data Instansi', '/institutions', 'building-2', 'institutions.view', []],
                ['Data Produk', '/products', 'box', 'products.view', []],
                ['Sistem Cicilan', '/installments', 'calendar-clock', 'installments.view', []],
                ['Sistem Bunga', '/methods', 'badge-percent', 'methods.view', []],
                ['Komite Kredit', '/committees', 'gavel', 'committees.view', []],
                ['Data Wilayah', '/regions', 'map-pin', 'regions.view', []],
            ]],
            ['Agunan', null, 'shield-check', null, [
                ['Jenis Agunan', '/collateral-types', 'landmark', 'collateral-types.view', []],
                ['Jenis Pengikatan', '/collateral-bindings', 'lock', 'collateral-bindings.view', []],
                ['Kondisi Agunan', '/collateral-conditions', 'triangle-alert', 'collateral-conditions.view', []],
                ['Metode Hitung', '/collateral-methods', 'calculator', 'collateral-methods.view', []],
            ]],
            ['Simulasi', null, 'flask-conical', null, [
                ['Agunan', '/collateral-simulation', 'shield-check', 'collateral-simulation.view', []],
                ['Pengajuan', '/loan-simulation', 'file-text', 'loan-simulation.view', []],
                ['Penjadwalan', '/scheduling-simulation', 'calendar-check', 'scheduling-simulation.view', []],
                ['Survei', '/survey-simulation', 'map-pinned', 'survey-simulation.view', []],
                ['Analisa', '/analysis-simulation', 'calculator', 'analysis-simulation.view', []],
                ['Persetujuan', '/approval-simulation', 'gavel', 'approval-simulation.view', []],
            ]],
        ],
        'admin' => [
            ['Kelola Perizinan', '/permissions', 'KeyRound', 'permissions.view', []],
            ['Kelola Peranan', '/roles', 'ShieldCheck', 'roles.view', []],
            ['Kelola Pengguna', '/users', 'UsersRound', 'users.view', []],
            ['Penampilan UI', '/appearance', 'Palette', 'appearance.view', []],
            ['Menu Navigasi', '/menus', 'ListTree', 'menus.view', []],
            ['Object Storage', '/object-storage', 'Database', 'storage.view', []],
            ['Skema Migrasi', '/schema-drafts', 'Table2', 'schema-drafts.view', []],
            ['Audit Trail Log', '/audit-trail', 'ScrollText', 'activity.view', []],
        ],
    ];

    public function run(): void
    {
        foreach (self::MENUS as $area => $items) {
            $this->seed($items, $area);
        }
    }

    private function seed(array $items, string $area, ?int $parentId = null): void
    {
        foreach ($items as $index => [$label, $href, $icon, $permission, $children]) {
            $menu = Menu::updateOrCreate(
                ['area' => $area, 'parent_id' => $parentId, 'label' => $label],
                ['href' => $href, 'icon' => $icon, 'permission' => $permission, 'sort' => $index, 'is_active' => true],
            );

            $this->seed($children, $area, $menu->id);
        }
    }
}
