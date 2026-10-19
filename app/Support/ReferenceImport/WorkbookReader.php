<?php

namespace App\Support\ReferenceImport;

use DateTimeInterface;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use OpenSpout\Reader\XLSX\Options;
use OpenSpout\Reader\XLSX\Reader;

/**
 * Reads the first sheet of an .xlsx as plain strings. Nothing in the file is ever run: a formula cell is read as the
 * value Excel cached for it, and a cell that merely looks like a formula is text.
 */
class WorkbookReader
{
    public const MAX_DATA_ROWS = 1000;

    /** Rows looked at, blank ones included, so a stray cell far down the sheet cannot make a request crawl. */
    private const MAX_SCANNED_ROWS = 5000;

    /**
     * @return array{header: list<string>, rows: array<int, list<string>>} data rows keyed by their row number in the sheet, blank rows left out
     */
    public function read(string $path): array
    {
        $reader = new Reader(new Options(SHOULD_PRESERVE_EMPTY_ROWS: true));

        try {
            $reader->open($path);
            $sheets = $reader->getSheetIterator();
            $sheets->rewind();

            return $this->collect($sheets->current()->getRowIterator());
        } catch (IOException|ReaderNotOpenedException) {
            throw new ImportRefused('Berkas bukan buku kerja Excel (.xlsx) yang bisa dibaca. Pakai template dari tombol "Unduh template".');
        } finally {
            $reader->close();
        }
    }

    /**
     * @param  iterable<int, Row>  $rows
     * @return array{header: list<string>, rows: array<int, list<string>>}
     */
    private function collect(iterable $rows): array
    {
        $header = null;
        $data = [];

        foreach ($rows as $number => $row) {
            if ($number > self::MAX_SCANNED_ROWS) {
                throw new ImportRefused('Berkas memuat terlalu banyak baris. Batasnya '.self::MAX_DATA_ROWS.' baris data.');
            }

            $cells = array_map($this->text(...), $row->toArray());
            $cells = array_values($cells);

            if ($header === null) {
                $header = $cells;

                continue;
            }

            if (array_filter($cells, fn (string $cell): bool => $cell !== '') === []) {
                continue;
            }

            if (count($data) >= self::MAX_DATA_ROWS) {
                throw new ImportRefused('Berkas memuat lebih dari '.self::MAX_DATA_ROWS.' baris data. Pecah menjadi beberapa berkas.');
            }

            $data[$number] = $cells;
        }

        if ($header === null || array_filter($header, fn (string $cell): bool => $cell !== '') === []) {
            throw new ImportRefused('Berkas kosong. Pakai template dari tombol "Unduh template".');
        }

        return ['header' => $header, 'rows' => $data];
    }

    private function text(mixed $value): string
    {
        return match (true) {
            $value === null => '',
            is_float($value) && $value == (int) $value && abs($value) < 1e15 => (string) (int) $value,
            $value instanceof DateTimeInterface => $value->format('Y-m-d'),
            is_scalar($value) => trim((string) $value),
            default => '',
        };
    }
}
