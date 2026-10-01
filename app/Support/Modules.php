<?php

namespace App\Support;

use App\Models\Permission;

/**
 * Registry of the modules and abilities that make up permission names: `<module>.<ability>`.
 * Single source of truth for the permission seeder, route middleware and the role matrix UI.
 */
class Modules
{
    public const ABILITY_LABELS = [
        'view' => 'Lihat',
        'manage' => 'Kelola',
        'view_any' => 'Lihat semua',
        'create' => 'Buat',
        'update' => 'Ubah',
        'delete' => 'Hapus',
        'delete_any' => 'Hapus semua',
    ];

    /** Actions offered by the permission generator. */
    public const STANDARD_ACTIONS = ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];

    public const MAP = [
        'dashboard' => ['label' => 'Dashboard', 'group' => 'Umum', 'abilities' => ['view']],
        'loan-applications' => ['label' => 'Pengajuan Kredit', 'group' => 'Kredit', 'abilities' => ['view', 'manage']],
        'collaterals' => ['label' => 'Jaminan', 'group' => 'Kredit', 'abilities' => ['view', 'manage']],
        'scheduling' => ['label' => 'Penjadwalan Survei', 'group' => 'Kredit', 'abilities' => ['view', 'manage']],
        'surveys' => ['label' => 'Survei', 'group' => 'Kredit', 'abilities' => ['view', 'manage']],
        'analysis' => ['label' => 'Analisa Kredit', 'group' => 'Kredit', 'abilities' => ['view']],
    ];

    /**
     * The modules that carry permissions. Reference data and access management (Referensi, Hak Akses) are Super Admin only, by role.
     *
     * @return array<string, array{label: string, group: string, abilities: array<int, string>}>
     */
    public static function map(): array
    {
        return self::MAP;
    }

    /**
     * @return list<string> the permissions defined in code (system), e.g. "surveys.view".
     */
    public static function systemPermissions(): array
    {
        $names = [];

        foreach (self::map() as $key => $module) {
            foreach ($module['abilities'] as $ability) {
                $names[] = "{$key}.{$ability}";
            }
        }

        return $names;
    }

    /**
     * @return list<string> permissions created from the generator, kept in the database.
     */
    public static function customPermissions(): array
    {
        return array_values(Permission::query()->where('is_custom', true)->orderBy('name')->pluck('name')->all());
    }

    /**
     * @return list<string> every permission a role may be given: system plus custom.
     */
    public static function permissions(): array
    {
        return [...self::systemPermissions(), ...self::customPermissions()];
    }

    /**
     * Matrix structure for the role editor, grouped for display.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function matrix(): array
    {
        $custom = collect(self::customPermissions())
            ->groupBy(fn (string $name): string => explode('.', $name, 2)[0])
            ->map(fn ($names, string $entity): array => [
                'key' => $entity,
                'label' => ucfirst(str_replace('-', ' ', $entity)),
                'group' => 'Kustom',
                'abilities' => $names->map(fn (string $name): array => [
                    'name' => $name,
                    'label' => self::ABILITY_LABELS[explode('.', $name, 2)[1]] ?? explode('.', $name, 2)[1],
                ])->values()->all(),
            ]);

        return collect(self::map())
            ->map(fn (array $module, string $key): array => [
                'key' => $key,
                'label' => $module['label'],
                'group' => $module['group'],
                'abilities' => array_map(fn (string $ability): array => [
                    'name' => "{$key}.{$ability}",
                    'label' => self::ABILITY_LABELS[$ability] ?? $ability,
                ], $module['abilities']),
            ])
            ->concat($custom)
            ->groupBy('group')
            ->map(fn ($modules, string $group): array => ['group' => $group, 'modules' => $modules->values()->all()])
            ->values()
            ->all();
    }
}
