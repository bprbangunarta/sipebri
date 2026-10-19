<?php

namespace App\Support\ReferenceImport;

use App\Support\ReferenceResource;
use OpenSpout\Common\Entity\Cell\StringCell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

/**
 * The workbook people fill in: a "Data" sheet whose header is the table's field labels, and a "Petunjuk" sheet with the limits.
 */
class Template
{
    /**
     * @return string path of a temporary .xlsx; the caller deletes it after sending
     */
    public function build(ReferenceResource $resource): string
    {
        $path = tempnam(sys_get_temp_dir(), 'tpl');
        $writer = new Writer;
        $writer->openToFile($path);

        $writer->getCurrentSheet()->setName('Data');
        $writer->addRow(Row::fromValues($resource->importHeader()));

        foreach ($resource->importExamples() as $example) {
            // Text cells keep a code such as 001 from turning into the number 1.
            $writer->addRow(new Row(array_map(fn (string $value): StringCell => new StringCell($value, null), $example)));
        }

        $writer->addNewSheetAndMakeItCurrent()->setName('Petunjuk');
        $writer->addRow(Row::fromValues(['Kolom', 'Wajib diisi', 'Maksimal karakter', 'Keterangan']));

        foreach ($resource->rawFields() as $field) {
            $writer->addRow(Row::fromValues([
                $field['label'],
                ($field['required'] ?? false) ? 'Ya' : 'Tidak',
                $field['max'] ?? 100,
                $field['hint'] ?? '',
            ]));
        }

        foreach ($this->notes($resource) as $note) {
            $writer->addRow(Row::fromValues([$note]));
        }

        $writer->close();

        return $path;
    }

    /**
     * @return list<string>
     */
    private function notes(ReferenceResource $resource): array
    {
        return [
            '',
            'Hapus dua baris contoh di sheet Data sebelum mengunggah, lalu isi data Anda mulai baris 2.',
            'Judul kolom di baris 1 jangan diubah. Sheet Data harus tetap sheet pertama.',
            'Paling banyak '.WorkbookReader::MAX_DATA_ROWS.' baris data dan 2 MB per berkas.',
            'Huruf kecil otomatis menjadi huruf besar. Kode berawalan nol (mis. 001): format kolom sebagai Teks sebelum mengetik.',
            'Kode yang sudah ada tidak menambah baris baru: nama '.mb_strtolower($resource->label()).' itu diperbarui, data lain yang memakainya tidak berubah.',
            'Jika satu baris salah, tidak ada yang disimpan; layar menampilkan nomor baris dan alasannya.',
        ];
    }
}
