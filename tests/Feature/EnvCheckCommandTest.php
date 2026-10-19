<?php

use Illuminate\Support\Facades\File;

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
    $files = envFiles("APP_NAME=Local\nDB_PASSWORD=local-secret\n", "APP_NAME=Example\nDB_PASSWORD=\nAPI_PER_MINUTE=120\nTRUSTED_PROXIES=\n");

    $this->artisan('env:check', [...$files, '--add' => true])->expectsOutputToContain('2 variabel ditambahkan')->assertSuccessful();

    $content = (string) file_get_contents($files['--env-file']);
    expect($content)->toContain("APP_NAME=Local\n")->toContain('DB_PASSWORD=local-secret')->toContain("API_PER_MINUTE=120\n")->toContain("TRUSTED_PROXIES=\n")->not->toContain('APP_NAME=Example');
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
})->with(['REDIS_URL', 'DB_URL', 'SENTRY_DSN', 'MAIL_PASSWD', 'AWS_CREDENTIALS_FILE', 'APP_SALT', 'SIGNING_PRIVATE', 'CODEX_TOKEN', 'CODEX_SECRET']);

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
    $files = envFiles("APP_ENV=production\nAPP_DEBUG=false\n", "APP_ENV=local\nAPP_DEBUG=true\nLOG_LEVEL=debug\nTRUSTED_PROXIES=*\nSESSION_SECURE_COOKIE=false\nAPP_URL=http://localhost:8010\nNEW_SAFE=1\n");

    $this->artisan('env:check', [...$files, '--add' => true])
        ->expectsOutputToContain('LOG_LEVEL tidak ditambahkan')
        ->expectsOutputToContain('TRUSTED_PROXIES tidak ditambahkan')
        ->expectsOutputToContain('SESSION_SECURE_COOKIE tidak ditambahkan')
        ->expectsOutputToContain('APP_URL tidak ditambahkan')
        ->assertFailed();

    $content = (string) file_get_contents($files['--env-file']);
    expect($content)->toContain('NEW_SAFE=1')->not->toContain('LOG_LEVEL=')->not->toContain('TRUSTED_PROXIES=')->not->toContain('APP_URL=')->toContain('APP_ENV=production');
});

it('still copies example values on a developer machine, outside production', function () {
    $files = envFiles("APP_ENV=local\n", "APP_ENV=local\nLOG_LEVEL=debug\n");

    $this->artisan('env:check', [...$files, '--add' => true])->assertSuccessful();

    expect((string) file_get_contents($files['--env-file']))->toContain('LOG_LEVEL=debug');
});
