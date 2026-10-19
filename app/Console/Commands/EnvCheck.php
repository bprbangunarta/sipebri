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
     * Example values that are fine on a developer machine but not on a server. Unless `.env` says `APP_ENV=local` or `testing`, `--add`
     * does not copy them: it names them and the person writes the real value. That includes `staging` and a `.env` with no `APP_ENV` at
     * all (Laravel itself then runs as production): a public staging with debug on is the same exposure.
     *
     * @var array<string, list<string>>
     */
    private const UNSAFE_IN_PRODUCTION = [
        'APP_DEBUG' => ['true', '1'],
        'LOG_LEVEL' => ['debug'],
        'SESSION_SECURE_COOKIE' => ['false', '0'],
    ];

    public function handle(): int
    {
        $envPath = (string) ($this->option('env-file') ?: base_path('.env'));
        $examplePath = (string) ($this->option('example-file') ?: base_path('.env.example'));

        foreach ([[$envPath, 'Berkas .env', 'salin dari .env.example lalu isi nilainya'], [$examplePath, 'Berkas contoh', 'periksa pilihan --example-file']] as [$path, $label, $hint]) {
            if (! file_exists($path)) {
                $this->components->error("{$label} tidak ada: {$hint} ({$path}).");

                return self::FAILURE;
            }

            if (! is_file($path)) {
                $this->components->error("{$label} bukan berkas biasa: {$path}");

                return self::FAILURE;
            }

            if (! is_readable($path) || filesize($path) > self::MAX_BYTES) {
                $this->components->error("Berkas tidak bisa dibaca atau terlalu besar untuk berkas .env: {$path}");

                return self::FAILURE;
            }
        }

        $duplicates = [];
        $exampleDuplicates = [];
        $example = $this->read((string) file_get_contents($examplePath), $exampleDuplicates);
        $env = $this->read((string) file_get_contents($envPath), $duplicates);
        $required = array_filter($example, fn (array $entry): bool => ! $entry['commented']);

        if ($required === []) {
            $this->components->error("Berkas contoh tidak memuat variabel sama sekali, jadi tidak ada yang bisa dibandingkan: {$examplePath}");

            return self::FAILURE;
        }

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
            $shown = $this->shown($key, $entry['value']);
            $this->components->warn("Kurang di .env: {$key} (contoh: {$shown})");
        }

        if (! $this->option('add')) {
            $this->components->error(count($missing).' variabel kurang. Jalankan `php artisan env:check --add` untuk menambahkannya dengan nilai contoh, lalu periksa nilainya.');

            return self::FAILURE;
        }

        return $this->append($envPath, $required);
    }

    /**
     * Appends the missing variables with their example values, except the ones unsafe for a production `.env`. `.env` is read once and
     * both the missing set and the new content come from that one text, so two runs at once never add a variable twice (each write is a
     * whole-file replacement, so a reader sees the old file or the new one, never a mix).
     *
     * @param  array<string, array{value: string, commented: bool}>  $required
     */
    private function append(string $envPath, array $required): int
    {
        $target = realpath($envPath) ?: $envPath;
        $content = (string) file_get_contents($target);
        $duplicates = [];
        $env = $this->read($content, $duplicates);
        $missing = array_diff_key($required, $env);
        $withheld = [];

        if (! $this->isDeveloperMachine($env)) {
            $withheld = array_filter($missing, fn (array $entry, string $key): bool => $this->unsafeInProduction($key, $entry['value']), ARRAY_FILTER_USE_BOTH);
            $missing = array_diff_key($missing, $withheld);
        }

        foreach ($withheld as $key => $entry) {
            $this->components->warn("{$key} tidak ditambahkan: nilai contohnya ({$this->shown($key, $entry['value'])}) tidak aman untuk produksi. Tulis nilai produksinya sendiri di .env.");
        }

        if ($missing !== []) {
            $lines = array_map(fn (string $key, array $entry): string => "{$key}={$entry['value']}", array_keys($missing), $missing);
            $updated = rtrim($content, "\r\n")."\n\n# Ditambahkan oleh `php artisan env:check --add` dari .env.example: periksa nilainya.\n".implode("\n", $lines)."\n";

            if (! is_writable($target) || ! $this->replaceFile($target, $updated)) {
                $this->components->error("Berkas .env tidak bisa ditulis ({$target}); isinya tidak diubah. Periksa izin berkas dan foldernya.");

                return self::FAILURE;
            }

            $this->components->info(count($missing).' variabel ditambahkan ke .env dengan nilai dari .env.example; periksa nilainya.');
        }

        return $withheld === [] ? self::SUCCESS : self::FAILURE;
    }

    /** What may be shown of an example value: never one that looks secret (a URL can carry a password). */
    private function shown(string $key, string $value): string
    {
        return $value === '' ? 'kosong' : (preg_match(self::SECRET, $key) === 1 ? '(rahasia)' : $value);
    }

    /**
     * @param  array<string, array{value: string, commented: bool}>  $env
     */
    private function isDeveloperMachine(array $env): bool
    {
        return isset($env['APP_ENV']) && ! $env['APP_ENV']['commented'] && in_array(strtolower(trim($env['APP_ENV']['value'], " \t\"'")), ['local', 'testing'], true);
    }

    private function unsafeInProduction(string $key, string $value): bool
    {
        $value = strtolower(trim($value, " \t\"'"));

        return in_array($value, self::UNSAFE_IN_PRODUCTION[$key] ?? [], true) || ($key === 'APP_URL' && str_starts_with($value, 'http://'));
    }

    /**
     * Writes through a temporary file in the same folder and then replaces the old one: a failure halfway never leaves a half-written
     * `.env`, and the file's permissions are kept. `$path` is the real file (a link is resolved first), so a link stays a link.
     */
    private function replaceFile(string $path, string $content): bool
    {
        $temporary = $path.'.'.getmypid().'.part';
        $mode = (int) @fileperms($path) & 0777;
        $previous = umask(0o077);

        try {
            $written = @file_put_contents($temporary, $content);
        } finally {
            umask($previous);
        }

        if ($written !== strlen($content) || ! @chmod($temporary, $mode === 0 ? 0600 : $mode) || ! @rename($temporary, $path)) {
            @unlink($temporary);

            return false;
        }

        return true;
    }

    /**
     * Reads the text of one `.env` file the way Laravel does for the names that matter here: `KEY=value`, `export KEY=value`, spaces
     * around the name, and `#`-commented examples; a leading byte order mark and any line ending are accepted, and the lines inside a
     * multi-line quoted value are not variables. A key written twice (both active) is recorded in `$duplicates`; Laravel uses the
     * first one, so the first one is kept.
     *
     * @param  list<string>  $duplicates
     * @return array<string, array{value: string, commented: bool}>
     */
    private function read(string $text, array &$duplicates): array
    {
        $entries = [];
        $quote = null;

        foreach (preg_split('/\r\n|\n|\r/', (string) preg_replace('/^\xEF\xBB\xBF/', '', $text)) ?: [] as $line) {
            if ($quote !== null) {
                $quote = $this->closes($line, $quote) ? null : $quote;

                continue;
            }

            if (preg_match('/^\s*(#\s*)?(?:export\s+)?([A-Za-z_][A-Za-z0-9_.]*)\s*=(.*)$/', $line, $match) !== 1) {
                continue;
            }

            $key = $match[2];
            $commented = $match[1] !== '';
            $value = rtrim($match[3]);
            $first = ltrim($value)[0] ?? '';

            if (! $commented && ($first === '"' || $first === "'") && ! $this->closes(substr(ltrim($value), 1), $first)) {
                $quote = $first;
            }

            if (isset($entries[$key]) && ! $entries[$key]['commented'] && ! $commented) {
                $duplicates[] = $key;

                continue;
            }

            // A `#` line without an active counterpart is only an optional example; the active line wins.
            if (! isset($entries[$key]) || ! $commented) {
                $entries[$key] = ['value' => $value, 'commented' => $commented];
            }
        }

        return $entries;
    }

    /** Whether the text holds the closing quote: a double quote may be escaped with a backslash, a single quote may not. */
    private function closes(string $text, string $quote): bool
    {
        return preg_match($quote === '"' ? '/(?<!\\\\)"/' : "/'/", $text) === 1;
    }
}
