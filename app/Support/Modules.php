<?php

namespace App\Support;

/**
 * Registry of the modules and abilities that make up permission names: `<module>.<ability>`.
 * Single source of truth for the permission seeder, route middleware and the role matrix UI.
 */
class Modules
{
    public const ABILITY_LABELS = [
        'view' => 'View',
        'manage' => 'Manage',
    ];

    public const MAP = [
        'dashboard' => ['label' => 'Dashboard', 'group' => 'General', 'abilities' => ['view']],
        'loan-applications' => ['label' => 'Loan Applications', 'group' => 'Credit', 'abilities' => ['view', 'manage']],
        'collaterals' => ['label' => 'Collaterals', 'group' => 'Credit', 'abilities' => ['view', 'manage']],
        'scheduling' => ['label' => 'Survey Scheduling', 'group' => 'Credit', 'abilities' => ['view', 'manage']],
        'surveys' => ['label' => 'Surveys', 'group' => 'Credit', 'abilities' => ['view', 'manage']],
        'analysis' => ['label' => 'Credit Analysis', 'group' => 'Credit', 'abilities' => ['view']],
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
     * @return list<string> every permission name, e.g. "committees.view".
     */
    public static function permissions(): array
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
     * Matrix structure for the role editor, grouped for display.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function matrix(): array
    {
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
            ->groupBy('group')
            ->map(fn ($modules, string $group): array => ['group' => $group, 'modules' => $modules->values()->all()])
            ->values()
            ->all();
    }
}
