<?php

namespace App\Support;

use App\Enums\RoleName;
use App\Models\User;

/**
 * Sidebar structure. Items are filtered by the user's permissions on the server, so users
 * never receive links to screens they cannot open (routes enforce the same permissions).
 */
class Navigation
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function for(User $user): array
    {
        $sections = [];

        foreach (self::definition() as $section) {
            $items = [];

            foreach ($section['items'] as $item) {
                if (isset($item['children'])) {
                    $children = array_values(array_filter($item['children'], fn (array $child): bool => self::allowed($user, $child)));

                    if ($children !== []) {
                        $items[] = ['label' => $item['label'], 'icon' => $item['icon'], 'children' => array_map(self::link(...), $children)];
                    }

                    continue;
                }

                if (self::allowed($user, $item)) {
                    $items[] = self::link($item);
                }
            }

            if ($items !== []) {
                $sections[] = ['label' => $section['label'], 'items' => $items];
            }
        }

        return $sections;
    }

    /**
     * An item opens for the holder of its permission, or, for the Super Admin-only areas, for the role.
     *
     * @param  array<string, mixed>  $item
     */
    private static function allowed(User $user, array $item): bool
    {
        return isset($item['role']) ? $user->hasRole($item['role']) : $user->can($item['permission']);
    }

    /**
     * First screen the user may open, used to land people who lack access to the dashboard.
     */
    public static function firstHref(User $user): ?string
    {
        foreach (self::for($user) as $section) {
            foreach ($section['items'] as $item) {
                $href = $item['href'] ?? ($item['children'][0]['href'] ?? null);

                if ($href !== null) {
                    return $href;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private static function link(array $item): array
    {
        return ['label' => $item['label'], 'icon' => $item['icon'] ?? null, 'href' => route($item['route'], $item['params'] ?? [], absolute: false), 'group' => $item['group'] ?? null];
    }

    /**
     * @return list<array{label: ?string, items: list<array<string, mixed>>}>
     */
    private static function definition(): array
    {
        return [
            ['label' => null, 'items' => [
                ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard', 'permission' => 'dashboard.view'],
                ['label' => 'Pengajuan', 'icon' => 'file-text', 'route' => 'loan-applications.index', 'permission' => 'loan-applications.view'],
                ['label' => 'Jaminan', 'icon' => 'landmark', 'route' => 'collaterals.index', 'permission' => 'collaterals.view'],
                ['label' => 'Penjadwalan', 'icon' => 'calendar', 'route' => 'scheduling.index', 'permission' => 'scheduling.view'],
                ['label' => 'Proses Survey', 'icon' => 'map-pin', 'route' => 'surveys.index', 'permission' => 'surveys.view'],
                ['label' => 'Analisa Kredit', 'icon' => 'clipboard-check', 'route' => 'analysis.index', 'permission' => 'analysis.view'],
            ]],
            ['label' => 'Referensi', 'items' => [
                self::reference('Data Kantor', 'building', 'offices'),
                self::reference('Data Resort', 'map-pin', 'resorts'),
                self::reference('Data Wilayah', 'map', 'regions'),
                ['label' => 'Data Kredit', 'icon' => 'banknote', 'children' => [
                    self::reference('Produk', 'dot', 'products'),
                    self::reference('Bunga', 'dot', 'interest-methods'),
                    self::reference('Angsuran', 'dot', 'installments'),
                    ['label' => 'BMPK', 'icon' => 'dot', 'route' => 'bmpk.show', 'role' => RoleName::SuperAdmin->value],
                ]],
                ['label' => 'Data Agunan', 'icon' => 'landmark', 'children' => [
                    self::reference('Jenis', 'dot', 'collateral-types'),
                    self::reference('Klasifikasi', 'dot', 'collateral-classifications'),
                    self::reference('Pengikatan', 'dot', 'collateral-bindings'),
                    self::reference('Kondisi', 'dot', 'collateral-conditions'),
                    self::reference('Penilaian', 'dot', 'collateral-valuations'),
                ]],
                ['label' => 'Data Komite', 'icon' => 'gavel', 'route' => 'committees.index', 'role' => RoleName::SuperAdmin->value],
            ]],
            ['label' => 'Pengaturan', 'items' => [
                ['label' => 'Data Perizinan', 'icon' => 'key-round', 'route' => 'permissions.index', 'role' => RoleName::SuperAdmin->value],
                ['label' => 'Data Peranan', 'icon' => 'shield', 'route' => 'roles.index', 'role' => RoleName::SuperAdmin->value],
                ['label' => 'Data Pengguna', 'icon' => 'user-cog', 'route' => 'users.index', 'role' => RoleName::SuperAdmin->value],
                ['label' => 'Data Audit Log', 'icon' => 'scroll-text', 'route' => 'audit-logs.index', 'role' => RoleName::SuperAdmin->value],
            ]],
        ];
    }

    /**
     * A Data Master / Credit Setup page from the registry in config/master_data.php.
     *
     * @return array<string, mixed>
     */
    private static function reference(string $label, string $icon, string $slug): array
    {
        return ['label' => $label, 'icon' => $icon, 'route' => 'master-data.index', 'params' => ['resource' => $slug], 'role' => RoleName::SuperAdmin->value];
    }
}
