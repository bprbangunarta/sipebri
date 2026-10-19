<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

/*
 * `php artisan env:check`: `.env` is not in git, so a new variable in `.env.example` must be noticed and can be added without changing the
 * values already there.
 */

/**
 * @return array{'--env-file': string, '--example-file': string}
 */
function envFiles(string $env, string $example): array
{
    $dir = storage_path('framework/testing/env-'.getmypid());
    @mkdir($dir, 0777, true);
    file_put_contents("{$dir}/.env", $env);
    file_put_contents("{$dir}/.env.example", $example);

    return ['--env-file' => "{$dir}/.env", '--example-file' => "{$dir}/.env.example"];
}

afterEach(function () {
    File::deleteDirectory(storage_path('framework/testing/env-'.getmypid()));
});

it('reports the variables missing from .env and fails, without printing secret values', function () {
    $files = envFiles("APP_NAME=Local\nDB_PASSWORD=local-secret\n", "APP_NAME=Example\nAPI_KEY=secret-value\nAPI_PER_MINUTE=120\n# SECURITY_CSP=true\nDB_PASSWORD=\n");

    $this->artisan('env:check', $files)
        ->expectsOutputToContain('Kurang di .env: API_KEY (contoh: (rahasia))')
        ->expectsOutputToContain('Kurang di .env: API_PER_MINUTE (contoh: 120)')
        ->assertFailed();
});

it('does not require variables that only appear as #-commented examples, and passes when everything is present', function () {
    $files = envFiles("APP_NAME=Local\nAPI_PER_MINUTE=60\n", "APP_NAME=Example\nAPI_PER_MINUTE=120\n# SECURITY_CSP=true\n");

    $this->artisan('env:check', $files)->expectsOutputToContain('memuat semua variabel')->assertSuccessful();
});

it('names variables in .env that the example no longer knows, without failing', function () {
    $files = envFiles("APP_NAME=Local\nOLD_ACCESS=x\n", "APP_NAME=Example\n");

    $this->artisan('env:check', $files)->expectsOutputToContain('OLD_ACCESS ada di .env tetapi tidak dikenal')->assertSuccessful();
});

it('appends the missing variables with --add without changing existing values', function () {
    $files = envFiles("APP_NAME=Local\nDB_PASSWORD=local-secret\n", "APP_NAME=Example\nDB_PASSWORD=\nAPI_PER_MINUTE=120\nEMPTY_ONE=\n");

    $this->artisan('env:check', [...$files, '--add' => true])->expectsOutputToContain('2 variabel ditambahkan')->assertSuccessful();

    $content = (string) file_get_contents($files['--env-file']);
    expect($content)->toContain("APP_NAME=Local\n")->toContain('DB_PASSWORD=local-secret')->toContain("API_PER_MINUTE=120\n")->toContain("EMPTY_ONE=\n")->not->toContain('APP_NAME=Example');
    $this->artisan('env:check', $files)->assertSuccessful();
});

it('fails with a hint when .env does not exist yet', function () {
    $files = envFiles('x=1', "APP_NAME=Example\n");
    unlink($files['--env-file']);

    $this->artisan('env:check', $files)->expectsOutputToContain('Berkas .env tidak ada')->assertFailed();
});

it('reads quotes, export, = and # inside values, Windows line endings, and a file without a final newline', function () {
    $files = envFiles("APP_NAME=\"My Local\"\r\nexport API_KEY=abc=def#ghi\r\nAPI_PER_MINUTE=60", "APP_NAME=Example\nAPI_KEY=\nAPI_PER_MINUTE=120\nOPTION='x y'\n");

    $this->artisan('env:check', $files)->expectsOutputToContain('Kurang di .env: OPTION')->assertFailed();

    $this->artisan('env:check', [...$files, '--add' => true])->assertSuccessful();

    $content = (string) file_get_contents($files['--env-file']);
    expect($content)->toContain("API_PER_MINUTE=60\n")->toContain("OPTION='x y'\n")->not->toContain("\r\n\n")->and(substr_count($content, 'API_KEY='))->toBe(1);
});

it('names a key written twice without failing', function () {
    $files = envFiles("APP_NAME=One\nAPP_NAME=Two\n", "APP_NAME=Example\n");

    $this->artisan('env:check', $files)->expectsOutputToContain('APP_NAME ditulis lebih dari sekali')->assertSuccessful();
});

it('never prints an example value that looks secret, including URLs that can carry a password', function (string $key) {
    $files = envFiles("APP_NAME=Local\n", "APP_NAME=Example\n{$key}=value-that-must-not-show\n");

    $this->artisan('env:check', $files)
        ->expectsOutputToContain("Kurang di .env: {$key} (contoh: (rahasia))")
        ->doesntExpectOutputToContain('value-that-must-not-show')
        ->assertFailed();
})->with(['REDIS_URL', 'DB_URL', 'SENTRY_DSN', 'MAIL_PASSWD', 'AWS_CREDENTIALS_FILE', 'APP_SALT', 'SIGNING_PRIVATE', 'CODEX_TOKEN', 'CODEX_SECRET', 'BASIC_AUTH_HEADER', 'CLIENT_CERT_BODY']);

it('refuses a file that cannot be read or is too large, changing nothing', function () {
    $files = envFiles(str_repeat("A=1\n", 400_000), "APP_NAME=Example\n");
    $before = md5_file($files['--env-file']);

    $this->artisan('env:check', [...$files, '--add' => true])->expectsOutputToContain('terlalu besar')->assertFailed();

    expect(md5_file($files['--env-file']))->toBe($before);

    if (function_exists('posix_geteuid') && posix_geteuid() === 0) {
        return;
    }

    chmod($files['--env-file'], 0000);

    try {
        $this->artisan('env:check', $files)->expectsOutputToContain('tidak bisa dibaca')->assertFailed();
    } finally {
        chmod($files['--env-file'], 0600);
    }
});

it('leaves the old content untouched when writing fails, and keeps the file permissions when it succeeds', function () {
    $files = envFiles("APP_NAME=Local\n", "APP_NAME=Example\nNEW=1\n");
    chmod($files['--env-file'], 0640);

    $this->artisan('env:check', [...$files, '--add' => true])->assertSuccessful();

    expect(substr(sprintf('%o', fileperms($files['--env-file'])), -4))->toBe('0640')->and(file_exists($files['--env-file'].'.part'))->toBeFalse();

    // The temporary file cannot be created inside a folder that is not writable: the old .env stays intact.
    $dir = dirname($files['--env-file']);
    $old = file_get_contents($files['--env-file']);
    file_put_contents($files['--example-file'], "APP_NAME=Example\nNEW=1\nMORE=2\n");
    chmod($dir, 0555);

    try {
        $this->artisan('env:check', [...$files, '--add' => true])->expectsOutputToContain('tidak bisa ditulis')->assertFailed();
    } finally {
        chmod($dir, 0755);
    }

    expect(file_get_contents($files['--env-file']))->toBe($old);
})->skip(fn () => function_exists('posix_geteuid') && posix_geteuid() === 0, 'root writes into a locked folder'); // weakening-allowed: root ignores folder permissions, so the failure cannot be produced

it('does not copy example values that are unsafe for a production .env, and names them', function () {
    $files = envFiles("APP_ENV=production\nAPP_DEBUG=false\n", "APP_ENV=local\nAPP_DEBUG=true\nLOG_LEVEL=debug\nSESSION_SECURE_COOKIE=false\nAPP_URL=http://localhost:8010\nNEW_SAFE=1\n");

    $this->artisan('env:check', [...$files, '--add' => true])
        ->expectsOutputToContain('LOG_LEVEL tidak ditambahkan')
        ->expectsOutputToContain('SESSION_SECURE_COOKIE tidak ditambahkan')
        ->expectsOutputToContain('APP_URL tidak ditambahkan')
        ->assertFailed();

    $content = (string) file_get_contents($files['--env-file']);
    expect($content)->toContain('NEW_SAFE=1')->not->toContain('LOG_LEVEL=')->not->toContain('SESSION_SECURE_COOKIE=')->not->toContain('APP_URL=')->toContain('APP_ENV=production');
});

it('treats APP_ENV=Production like production and withholds a debug flag the .env does not have yet', function (string $production) {
    $files = envFiles("APP_ENV={$production}\n", "APP_ENV=local\nAPP_DEBUG=TRUE\nSESSION_SECURE_COOKIE=0\nNEW_SAFE=1\n");

    $this->artisan('env:check', [...$files, '--add' => true])
        ->expectsOutputToContain('APP_DEBUG tidak ditambahkan')
        ->expectsOutputToContain('SESSION_SECURE_COOKIE tidak ditambahkan')
        ->assertFailed();

    expect((string) file_get_contents($files['--env-file']))->toContain('NEW_SAFE=1')->not->toContain('APP_DEBUG');
})->with(['production', 'Production', '"production"', 'staging', 'Staging', '']);

it('also withholds unsafe example values when .env has no APP_ENV at all, as Laravel then runs as production', function () {
    $files = envFiles("APP_NAME=Live\n", "APP_ENV=local\nAPP_DEBUG=true\nNEW_SAFE=1\n");

    $this->artisan('env:check', [...$files, '--add' => true])->expectsOutputToContain('APP_DEBUG tidak ditambahkan')->assertFailed();

    expect((string) file_get_contents($files['--env-file']))->toContain('NEW_SAFE=1')->not->toContain('APP_DEBUG');
});

it('copies example values on a developer machine that says testing too', function () {
    $files = envFiles("APP_ENV=testing\n", "APP_ENV=local\nLOG_LEVEL=debug\n");

    $this->artisan('env:check', [...$files, '--add' => true])->assertSuccessful();

    expect((string) file_get_contents($files['--env-file']))->toContain('LOG_LEVEL=debug');
});

it('still copies example values on a developer machine, outside production', function () {
    $files = envFiles("APP_ENV=local\n", "APP_ENV=local\nLOG_LEVEL=debug\n");

    $this->artisan('env:check', [...$files, '--add' => true])->assertSuccessful();

    expect((string) file_get_contents($files['--env-file']))->toContain('LOG_LEVEL=debug');
});

it('reads the keys the way Laravel does: indented, spaced before the equals sign, lowercase, after a byte order mark, or with old Mac line endings', function () {
    $files = envFiles("\u{FEFF}APP_NAME=Local\n  INDENTED=1\nSPACED =1\nlower_case=1\r", "APP_NAME=Example\nINDENTED=0\nSPACED=0\nlower_case=0\n");

    $this->artisan('env:check', $files)->expectsOutputToContain('memuat semua variabel')->assertSuccessful();
});

it('asks for a lowercase variable of the example and accepts old Mac line endings in .env', function () {
    $files = envFiles("FIRST=1\rSECOND=2\r", "FIRST=1\nSECOND=2\nlower_case=1\nTHIRD=3\n");

    $exit = Artisan::call('env:check', $files);
    $output = Artisan::output();

    expect($exit)->toBe(1)
        ->and($output)->toContain('Kurang di .env: lower_case')->toContain('Kurang di .env: THIRD')
        ->not->toContain('Kurang di .env: SECOND');
});

it('does not take a line inside a multi-line quoted value for a variable', function () {
    $files = envFiles("APP_NAME=Local\nCERT=\"-----BEGIN-----\nINSIDE=1\n-----END-----\"\nAFTER=1\n", "APP_NAME=Example\nCERT=\nAFTER=1\n");

    $this->artisan('env:check', $files)->doesntExpectOutputToContain('INSIDE')->expectsOutputToContain('memuat semua variabel')->assertSuccessful();
});

it('fails when the example holds no variable at all, instead of calling an empty check a pass', function () {
    $files = envFiles("APP_NAME=Local\n", "# only comments\n\n");

    $this->artisan('env:check', $files)->expectsOutputToContain('tidak memuat variabel')->assertFailed();
});

it('says a folder is not a file instead of saying the file does not exist', function () {
    $files = envFiles('x', "APP_NAME=Example\n");
    unlink($files['--env-file']);
    mkdir($files['--env-file']);

    $this->artisan('env:check', $files)->expectsOutputToContain('bukan berkas')->assertFailed();
});

it('never prints the value of a withheld example variable that looks secret', function () {
    $files = envFiles("APP_ENV=production\n", "APP_ENV=local\nAPP_URL=http://user:topsecret@example.test\n");

    // The buffered output is read as a whole: the expectation helpers stop checking a line once it matches an expected text.
    $exit = Artisan::call('env:check', [...$files, '--add' => true]);

    expect($exit)->toBe(1)
        ->and(Artisan::output())->toContain('APP_URL tidak ditambahkan')->not->toContain('topsecret');
});

it('writes through a symbolic link into the real file and keeps the link', function () {
    $files = envFiles("APP_NAME=Local\n", "APP_NAME=Example\nNEW=1\n");
    $real = dirname($files['--env-file']).'/real.env';
    rename($files['--env-file'], $real);
    symlink($real, $files['--env-file']);

    $this->artisan('env:check', [...$files, '--add' => true])->assertSuccessful();

    expect(is_link($files['--env-file']))->toBeTrue()->and((string) file_get_contents($real))->toContain("NEW=1\n");
});

it('refuses to change a .env that is marked read-only', function () {
    $files = envFiles("APP_NAME=Local\n", "APP_NAME=Example\nNEW=1\n");
    chmod($files['--env-file'], 0444);

    $this->artisan('env:check', [...$files, '--add' => true])->expectsOutputToContain('tidak bisa ditulis')->assertFailed();

    expect((string) file_get_contents($files['--env-file']))->toBe("APP_NAME=Local\n");
})->skip(fn () => function_exists('posix_geteuid') && posix_geteuid() === 0, 'root writes to a read-only file'); // weakening-allowed: root ignores file permissions, so the refusal cannot be produced

it('adds each missing variable exactly once when several runs start at the same time', function () {
    $files = envFiles("APP_NAME=Local\n", "APP_NAME=Example\nA1=1\nA2=2\nA3=3\nA4=4\nA5=5\n");

    for ($round = 0; $round < 3; $round++) {
        file_put_contents($files['--env-file'], "APP_NAME=Local\n");

        $results = Process::pool(function ($pool) use ($files): void {
            for ($i = 0; $i < 8; $i++) {
                $pool->command([PHP_BINARY, base_path('artisan'), 'env:check', '--add', "--env-file={$files['--env-file']}", "--example-file={$files['--example-file']}"]);
            }
        })->start()->wait();

        $failed = array_filter(iterator_to_array($results), fn ($result): bool => ! $result->successful());
        $content = (string) file_get_contents($files['--env-file']);

        expect($failed)->toBe([], "round {$round}")
            ->and(substr_count($content, "A1=1\n"))->toBe(1, "round {$round}")
            ->and(glob(dirname($files['--env-file']).'/*.part'))->toBe([]);
    }
});
