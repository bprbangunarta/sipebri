<?php

/*
 * `.env.example` is what people copy into `.env`: every project variable read by `config/*.php` must be there (set, or as a `#` example),
 * and a project variable there must still be read by the configuration. A mismatch otherwise only shows on the server.
 * Project variables are the ones this application added; Laravel's own (DB_, MAIL_HOST, REDIS_, ...) are documented by Laravel. A
 * variable counts as Laravel's when it starts with one of the framework's prefixes, so a new variable under any other name is a project
 * variable and must be in the example without anyone remembering to extend a list.
 */
const FRAMEWORK_ENV_PATTERN = '/^(APP_|AUTH_|DB_|CACHE_|MEMCACHED_|REDIS_|DYNAMODB_|LOG_|MAIL_|BEANSTALKD_|SQS_|QUEUE_|SESSION_|AWS_|MYSQL_|POSTMARK_|RESEND_|SLACK_|PAPERTRAIL_|FILESYSTEM_)/';

/** Variables the framework or Vite reads itself, so no configuration file of ours names them. */
const ENV_READ_OUTSIDE_CONFIG = ['PHP_CLI_SERVER_WORKERS', 'BCRYPT_ROUNDS', 'BROADCAST_CONNECTION', 'VITE_APP_NAME'];

/** Variables under a framework prefix that this application added. */
const PROJECT_ENV_UNDER_FRAMEWORK_PREFIX = ['MAIL_TIMEOUT', 'SESSION_COOKIE'];

function isProjectEnvKey(string $key): bool
{
    return preg_match(FRAMEWORK_ENV_PATTERN, $key) !== 1 || in_array($key, PROJECT_ENV_UNDER_FRAMEWORK_PREFIX, true);
}

/**
 * Variables read with `env('KEY'...)` in every config file, mapped to the files that read them.
 *
 * @return array<string, list<string>>
 */
function configEnvKeys(): array
{
    $keys = [];

    foreach (glob(config_path('*.php')) ?: [] as $file) {
        preg_match_all('/\b(?:env|getenv|Env::get)\(\s*([\'"])([A-Z0-9_]+)\1/', (string) file_get_contents($file), $found);

        foreach (array_unique($found[2]) as $key) {
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
    $read = array_filter(array_keys(configEnvKeys()), isProjectEnvKey(...));

    expect($read)->not->toBeEmpty()
        ->and(array_values(array_diff($read, envExampleKeys())))->toBe([]);
});

it('keeps no project variable in .env.example that nothing reads', function () {
    $read = array_keys(configEnvKeys());
    $dead = array_values(array_filter(envExampleKeys(), fn (string $key): bool => isProjectEnvKey($key) && ! in_array($key, $read, true) && ! in_array($key, ENV_READ_OUTSIDE_CONFIG, true)));

    expect($dead)->toBe([]);
});

it('does not ship an empty project variable in .env.example that overrides a config default (KEY= is not the same as unset)', function () {
    preg_match_all('/^([A-Z][A-Z0-9_]+)=\s*$/m', (string) file_get_contents(base_path('.env.example')), $empty);
    $overridden = [];

    foreach (configEnvKeys() as $key => $files) {
        if (! isProjectEnvKey($key) || ! in_array($key, $empty[1], true)) {
            continue;
        }

        foreach ($files as $file) {
            // `env('KEY', <default>)` yields '' (not the default) when .env holds `KEY=`; `env('KEY') ?: <default>` is safe.
            if (preg_match("/env\\(\\s*['\"]{$key}['\"],\\s*(?!null\\))[^)]/", (string) file_get_contents($file)) === 1) {
                $overridden[] = basename($file).': '.$key;
            }
        }
    }

    expect($overridden)->toBe([]);
});

it('names every variable of a configuration file by a literal, so this check can see it', function () {
    $dynamic = [];

    foreach (glob(config_path('*.php')) ?: [] as $file) {
        if (preg_match('/\b(?:env|getenv)\(\s*(?![\'"])\S/', (string) file_get_contents($file)) === 1) {
            $dynamic[] = basename($file);
        }
    }

    expect($dynamic)->toBe([]);
});

it('reads the environment only in the configuration files', function () {
    $offenders = [];

    foreach (['app', 'bootstrap', 'routes', 'database', 'resources/views', 'lang'] as $dir) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($dir), FilesystemIterator::SKIP_DOTS)) as $file) {
            if ($file->getExtension() !== 'php' || str_contains($file->getPathname(), '/bootstrap/cache/')) {
                continue;
            }

            if (preg_match('/\b(?:env|getenv)\(|\$_(?:ENV|SERVER)\[|\bEnv::/', (string) file_get_contents($file->getPathname())) === 1) {
                $offenders[] = substr($file->getPathname(), strlen(base_path()) + 1);
            }
        }
    }

    expect($offenders)->toBe([]);
});

it('blanks the Codex address and credentials in phpunit.xml, so no test can reach the real service', function () {
    $phpunit = (string) file_get_contents(base_path('phpunit.xml'));
    $codex = ['CODEX_ENDPOINT', 'CODEX_TOKEN', 'CODEX_ID', 'CODEX_SECRET'];

    expect(array_diff($codex, array_keys(configEnvKeys())))->toBe([], 'The configuration no longer reads one of these: update this test.');

    foreach ($codex as $key) {
        expect($phpunit)->toContain("<env name=\"{$key}\" value=\"\"/>");
    }
});

it('holds variables only: every comment line is an optional variable written as "# NAME=value", and the explanations live in docs/deployment.md', function () {
    $prose = [];

    foreach (explode("\n", (string) file_get_contents(base_path('.env.example'))) as $number => $line) {
        if (str_starts_with($line, '#') && preg_match('/^# [A-Z][A-Z0-9_]*=("[^"]*"|\S*)$/', $line) !== 1) {
            $prose[] = ($number + 1).': '.$line;
        }
    }

    expect($prose)->toBe([]);
    expect(base_path('docs/deployment.md'))->toBeFile();
});
