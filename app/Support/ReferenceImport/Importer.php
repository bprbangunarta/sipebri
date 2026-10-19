<?php

namespace App\Support\ReferenceImport;

use App\Audit\Audit;
use App\Support\ReferenceResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Applies a workbook to a reference table: new codes are created, a known code gets its other fields refreshed, and
 * nothing is ever deleted or re-keyed, so records that point at a row keep pointing at it. All or nothing.
 */
class Importer
{
    private const KEY = 'code';

    public function __construct(private readonly WorkbookReader $reader) {}

    /**
     * @return array{created: int, updated: int, unchanged: int}
     */
    public function run(ReferenceResource $resource, string $path): array
    {
        try {
            $result = $this->apply($resource, $this->parse($resource, $this->reader->read($path)));
        } catch (ImportRefused $refused) {
            Audit::record('references.imported', 'references', 'imported', null, context: ['resource' => $resource->slug, 'issues' => count($refused->issues)], outcome: 'failure');

            throw $refused;
        }

        Audit::record('references.imported', 'references', 'imported', null, new: $result, context: ['resource' => $resource->slug]);

        return $result;
    }

    /**
     * @param  array{header: list<string>, rows: array<int, list<string>>}  $sheet
     * @return list<array<string, mixed>> validated, normalised rows
     */
    private function parse(ReferenceResource $resource, array $sheet): array
    {
        $header = $resource->importHeader();
        $given = $sheet['header'];

        while ($given !== [] && end($given) === '') {
            array_pop($given);
        }

        if (array_map('mb_strtolower', $given) !== array_map('mb_strtolower', $header)) {
            throw new ImportRefused('Judul kolom tidak sesuai template. Baris 1 harus berisi: '.implode(', ', $header).'.');
        }

        if ($sheet['rows'] === []) {
            throw new ImportRefused('Berkas tidak berisi baris data. Isi data mulai baris 2.');
        }

        $names = array_column($resource->rawFields(), 'name');
        $existing = $this->existing($resource, $sheet['rows'], $names);
        $issues = [];
        $seen = [];
        $parsed = [];

        foreach ($sheet['rows'] as $number => $cells) {
            if (count(array_filter(array_slice($cells, count($names)), fn (string $cell): bool => $cell !== '')) > 0) {
                $issues[] = "Baris $number: ada isian di luar kolom template.";

                continue;
            }

            $input = $resource->normalize(array_combine($names, array_pad(array_slice($cells, 0, count($names)), count($names), '')));
            $record = $existing[$input[self::KEY] ?? ''] ?? null;
            $validator = validator($input, $resource->rules($record, $input), [], $resource->attributeNames());

            if ($validator->fails()) {
                array_push($issues, ...array_map(fn (string $message): string => "Baris $number: $message", $validator->errors()->all()));

                continue;
            }

            $key = (string) $input[self::KEY];

            if (isset($seen[$key])) {
                $issues[] = "Baris $number: kode $key sudah muncul di baris {$seen[$key]}.";

                continue;
            }

            $seen[$key] = $number;
            $parsed[] = $input;
        }

        if ($issues !== []) {
            throw new ImportRefused('Berkas ditolak: '.count($issues).' masalah ditemukan dan tidak ada yang disimpan.', $issues);
        }

        return $parsed;
    }

    /**
     * @param  array<int, list<string>>  $rows
     * @param  list<string>  $names
     * @return array<string, Model> records of the table keyed by their code
     */
    private function existing(ReferenceResource $resource, array $rows, array $names): array
    {
        $at = array_search(self::KEY, $names, true);
        $codes = array_values(array_unique(array_map(fn (array $cells): string => mb_strtoupper($cells[$at] ?? ''), $rows)));
        $found = [];

        foreach (array_chunk($codes, 500) as $chunk) {
            foreach ($resource->model()::query()->whereIn(self::KEY, $chunk)->get() as $record) {
                $found[(string) $record->getAttribute(self::KEY)] = $record;
            }
        }

        return $found;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array{created: int, updated: int, unchanged: int}
     */
    private function apply(ReferenceResource $resource, array $rows): array
    {
        $result = ['created' => 0, 'updated' => 0, 'unchanged' => 0];

        try {
            DB::transaction(function () use ($resource, $rows, &$result): void {
                foreach ($rows as $input) {
                    $record = $resource->model()::query()->where(self::KEY, $input[self::KEY])->first();

                    if ($record === null) {
                        $resource->model()::query()->create($input);
                        $result['created']++;

                        continue;
                    }

                    $record->fill($input);

                    if ($record->isDirty()) {
                        $record->save();
                        $result['updated']++;
                    } else {
                        $result['unchanged']++;
                    }
                }
            });
        } catch (UniqueConstraintViolationException) {
            throw new ImportRefused('Kode yang sama baru saja disimpan oleh orang lain, jadi impor dibatalkan dan tidak ada yang disimpan. Coba unggah lagi.');
        }

        return $result;
    }
}
