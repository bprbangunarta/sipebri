<?php

/*
 * `.env.example` is what people copy into `.env`: every project variable read by `config/*.php` must be there (set, or as a `#` example),
 * and a project variable there must still be read by the configuration. A mismatch otherwise only shows on the server.
 * Project variables are the ones this application added; Laravel's own (DB_, MAIL_HOST, REDIS_, ...) are documented by Laravel.
 */
const PROJECT_ENV_PATTERN = '/^(CODEX_|GEOCODER_|REVERSE_GEOCODING$|MFA_|AUDIT_|ATTACHMENTS_|MAIL_TIMEOUT$|SESSION_COOKIE$)/';

/**
 * Variables read with `env('KEY'...)` in every config file, mapped to the files that read them.
 *
 * @return array<string, list<string>>
 */
function configEnvKeys(): array
{
    $keys = [];

    foreach (glob(config_path('*.php')) ?: [] as $file) {
        preg_match_all("/env\(\s*'([A-Z0-9_]+)'/", (string) file_get_contents($file), $found);

        foreach (array_unique($found[1]) as $key) {
            $keys[$key][] = $file;
        }
    }

    return $keys;
}

/** @return list<string> */
function envExampleKeys(): array
{
    preg_match_all('/^#?\s*([A-Z][A-Z0-9_]+)=/m', (string) file_get_contents(base_path('.env.example')), $found);

    return array_values(array_unique($found[1]));
}

it('lists in .env.example every project variable the configuration reads', function () {
    $read = array_filter(array_keys(configEnvKeys()), fn (string $key): bool => preg_match(PROJECT_ENV_PATTERN, $key) === 1);

    expect($read)->not->toBeEmpty()
        ->and(array_values(array_diff($read, envExampleKeys())))->toBe([]);
});

it('keeps no project variable in .env.example that nothing reads', function () {
    $read = array_keys(configEnvKeys());
    $dead = array_values(array_filter(envExampleKeys(), fn (string $key): bool => preg_match(PROJECT_ENV_PATTERN, $key) === 1 && ! in_array($key, $read, true)));

    expect($dead)->toBe([]);
});

it('does not ship an empty project variable in .env.example that overrides a config default (KEY= is not the same as unset)', function () {
    preg_match_all('/^([A-Z][A-Z0-9_]+)=\s*$/m', (string) file_get_contents(base_path('.env.example')), $empty);
    $overridden = [];

    foreach (configEnvKeys() as $key => $files) {
        if (preg_match(PROJECT_ENV_PATTERN, $key) !== 1 || ! in_array($key, $empty[1], true)) {
            continue;
        }

        foreach ($files as $file) {
            // `env('KEY', <default>)` yields '' (not the default) when .env holds `KEY=`; `env('KEY') ?: <default>` is safe.
            if (preg_match("/env\\(\\s*'{$key}',\\s*(?!null\\))[^)]/", (string) file_get_contents($file)) === 1) {
                $overridden[] = basename($file).': '.$key;
            }
        }
    }

    expect($overridden)->toBe([]);
});
