<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Compares `.env` with `.env.example`. `.env` is not in git, so a variable added to the example never reaches an existing `.env`: this
 * command names the missing ones (and, with `--add`, appends them with the example value) and the ones no longer known. Values that look
 * secret are never printed, and a value already in `.env` is never changed.
 */
class EnvCheck extends Command
{
    protected $signature = 'env:check
        {--add : Tambahkan variabel yang kurang ke .env dengan nilai dari .env.example}
        {--env-file= : Berkas .env (bawaan: .env)}
        {--example-file= : Berkas contoh (bawaan: .env.example)}';

    protected $description = 'Membandingkan .env dengan .env.example: variabel yang kurang dan yang sudah tidak dikenal';

    private const SECRET = '/(KEY|SECRET|PASSWORD|PASSWD|TOKEN|URL|DSN|CREDENTIAL|AUTH|PRIVATE|SALT|CERT)/i';

    /** A file larger than this is not a `.env`; reading it whole only wastes memory. */
    private const MAX_BYTES = 1_048_576;

    /**
     * Example values that are fine on a developer machine but not in production. When `.env` already says `APP_ENV=production`, `--add`
     * does not copy them: it names them and the person writes the production value.
     *
     * @var array<string, list<string>>
     */
    private const UNSAFE_IN_PRODUCTION = [
        'APP_DEBUG' => ['true', '1'],
        'APP_ENV' => ['local', 'testing'],
        'LOG_LEVEL' => ['debug'],
        'SESSION_SECURE_COOKIE' => ['false', '0'],
        'TRUSTED_PROXIES' => ['*', '**'],
    ];

    public function handle(): int
    {
        $envPath = (string) ($this->option('env-file') ?: base_path('.env'));
        $examplePath = (string) ($this->option('example-file') ?: base_path('.env.example'));

        if (! is_file($envPath) || ! is_file($examplePath)) {
            $this->components->error(is_file($envPath) ? "Berkas contoh tidak ada: {$examplePath}" : "Berkas .env tidak ada: salin dari .env.example lalu isi nilainya ({$envPath}).");

            return self::FAILURE;
        }

        foreach ([$envPath, $examplePath] as $path) {
            if (! is_readable($path) || filesize($path) > self::MAX_BYTES) {
                $this->components->error("Berkas tidak bisa dibaca atau terlalu besar untuk berkas .env: {$path}");

                return self::FAILURE;
            }
        }

        $duplicates = [];
        $exampleDuplicates = [];
        $example = $this->read($examplePath, $exampleDuplicates);
        $env = $this->read($envPath, $duplicates);
        $required = array_filter($example, fn (array $entry): bool => ! $entry['commented']);
        $missing = array_diff_key($required, $env);
        $unknown = array_diff_key(array_filter($env, fn (array $entry): bool => ! $entry['commented']), $example);

        foreach ($duplicates as $key) {
            $this->components->warn("{$key} ditulis lebih dari sekali di .env: yang pertama yang dipakai Laravel.");
        }

        foreach ($unknown as $key => $_) {
            $this->components->warn("{$key} ada di .env tetapi tidak dikenal di .env.example (sudah tidak dipakai, atau salah ketik).");
        }

        if ($missing === []) {
            $this->components->info('.env memuat semua variabel dari .env.example.');

            return self::SUCCESS;
        }

        foreach ($missing as $key => $entry) {
            $shown = $entry['value'] === '' ? 'kosong' : (preg_match(self::SECRET, $key) === 1 ? '(rahasia)' : $entry['value']);
            $this->components->warn("Kurang di .env: {$key} (contoh: {$shown})");
        }

        if (! $this->option('add')) {
            $this->components->error(count($missing).' variabel kurang. Jalankan `php artisan env:check --add` untuk menambahkannya dengan nilai contoh, lalu periksa nilainya.');

            return self::FAILURE;
        }

        return $this->append($envPath, $env, $missing);
    }

    /**
     * Appends the missing variables with their example values, except the ones unsafe for a production `.env`.
     *
     * @param  array<string, array{value: string, commented: bool}>  $env
     * @param  array<string, array{value: string, commented: bool}>  $missing
     */
    private function append(string $envPath, array $env, array $missing): int
    {
        $withheld = [];

        if ($this->isProduction($env)) {
            $withheld = array_filter($missing, fn (array $entry, string $key): bool => $this->unsafeInProduction($key, $entry['value']), ARRAY_FILTER_USE_BOTH);
            $missing = array_diff_key($missing, $withheld);
        }

        foreach ($withheld as $key => $entry) {
            $this->components->warn("{$key} tidak ditambahkan: nilai contohnya ({$entry['value']}) tidak aman untuk produksi. Tulis nilai produksinya sendiri di .env.");
        }

        if ($missing !== []) {
            $lines = array_map(fn (string $key, array $entry): string => "{$key}={$entry['value']}", array_keys($missing), $missing);
            $content = (string) file_get_contents($envPath);
            $updated = rtrim($content, "\r\n")."\n\n# Ditambahkan oleh `php artisan env:check --add` dari .env.example: periksa nilainya.\n".implode("\n", $lines)."\n";

            if (! $this->replaceFile($envPath, $updated)) {
                $this->components->error("Berkas .env tidak bisa ditulis ({$envPath}); isinya tidak diubah. Periksa izin berkas dan foldernya.");

                return self::FAILURE;
            }

            $this->components->info(count($missing).' variabel ditambahkan ke .env dengan nilai dari .env.example; periksa nilainya.');
        }

        return $withheld === [] ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @param  array<string, array{value: string, commented: bool}>  $env
     */
    private function isProduction(array $env): bool
    {
        return isset($env['APP_ENV']) && ! $env['APP_ENV']['commented'] && strtolower(trim($env['APP_ENV']['value'], " \t\"'")) === 'production';
    }

    private function unsafeInProduction(string $key, string $value): bool
    {
        $value = strtolower(trim($value, " \t\"'"));

        return in_array($value, self::UNSAFE_IN_PRODUCTION[$key] ?? [], true) || ($key === 'APP_URL' && str_starts_with($value, 'http://'));
    }

    /**
     * Writes through a temporary file in the same folder and then replaces the old one: a failure halfway never leaves a half-written
     * `.env`, and the file's permissions are kept.
     */
    private function replaceFile(string $path, string $content): bool
    {
        $temporary = $path.'.part';
        $mode = (int) @fileperms($path) & 0777;
        $previous = umask(0o077);

        try {
            $written = @file_put_contents($temporary, $content);
        } finally {
            umask($previous);
        }

        if ($written === false || ! @chmod($temporary, $mode === 0 ? 0600 : $mode) || ! @rename($temporary, $path)) {
            @unlink($temporary);

            return false;
        }

        return true;
    }

    /**
     * Reads one `.env` file: `KEY=value`, `export KEY=value`, and `#`-commented examples; Windows line endings are dropped. A key written
     * twice (both active) is recorded in `$duplicates`; Laravel uses the first one, so the first one is kept.
     *
     * @param  list<string>  $duplicates
     * @return array<string, array{value: string, commented: bool}>
     */
    private function read(string $path, array &$duplicates): array
    {
        $entries = [];

        foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            $line = rtrim($line, "\r");

            if (preg_match('/^(#\s*)?(?:export\s+)?([A-Z][A-Z0-9_]*)=(.*)$/', $line, $match) !== 1) {
                continue;
            }

            $key = $match[2];
            $commented = $match[1] !== '';

            if (isset($entries[$key]) && ! $entries[$key]['commented'] && ! $commented) {
                $duplicates[] = $key;

                continue;
            }

            // A `#` line without an active counterpart is only an optional example; the active line wins.
            if (! isset($entries[$key]) || ! $commented) {
                $entries[$key] = ['value' => $match[3], 'commented' => $commented];
            }
        }

        return $entries;
    }
}
