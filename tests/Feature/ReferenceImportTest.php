<?php

use App\Audit\AuditLog;
use App\Models\Institution;
use App\Models\LoanApplication;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\UploadedFile;
use OpenSpout\Common\Entity\Cell\StringCell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;

/**
 * Builds an .xlsx upload from rows of plain cell values (the first row is the header).
 *
 * @param  list<list<string|int|null>>  $rows
 */
function workbook(array $rows, string $name = 'resorts.xlsx'): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'xlsx');
    $writer = new Writer;
    $writer->openToFile($path);

    foreach ($rows as $cells) {
        $writer->addRow(Row::fromValues($cells));
    }

    $writer->close();

    return new UploadedFile($path, $name, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
}

/**
 * An upload that is a zip archive holding exactly these parts.
 *
 * @param  array<string, string>  $parts
 */
function zipUpload(array $parts): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'xlsx');
    $zip = new ZipArchive;
    $zip->open($path, ZipArchive::OVERWRITE);

    foreach ($parts as $name => $content) {
        $zip->addFromString($name, $content);
    }

    $zip->close();

    return new UploadedFile($path, 'resorts.xlsx', null, null, true);
}

function truncatedWorkbook(): UploadedFile
{
    $bytes = (string) file_get_contents(workbook([['Kode', 'Nama'], ['R1', 'One']])->getPathname());
    $path = tempnam(sys_get_temp_dir(), 'xlsx');
    file_put_contents($path, substr($bytes, 0, intdiv(strlen($bytes), 2)));

    return new UploadedFile($path, 'resorts.xlsx', null, null, true);
}

function importResorts(UploadedFile $file)
{
    return test()->post(route('references.import', 'resorts'), ['file' => $file]);
}

function applicationAt(Institution $resort): LoanApplication
{
    return LoanApplication::create([
        'application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A',
        'created_by' => superAdmin()->id, 'institution_id' => $resort->id,
    ]);
}

beforeEach(function () {
    $this->actingAs(superAdmin());
});

it('serves a template built from the fields of the table', function () {
    $response = $this->get(route('references.import.template', 'resorts'))->assertOk();
    $path = $response->baseResponse->getFile()->getPathname();

    $reader = new Reader;
    $reader->open($path);
    $sheets = [];

    foreach ($reader->getSheetIterator() as $sheet) {
        $sheets[$sheet->getName()] = array_map(fn (Row $row): array => $row->toArray(), iterator_to_array($sheet->getRowIterator(), false));
    }

    $reader->close();

    expect(array_keys($sheets))->toBe(['Data', 'Petunjuk'])
        ->and($sheets['Data'][0])->toBe(['Kode', 'Nama'])
        ->and(count($sheets['Data']))->toBe(3)
        ->and(implode(' ', array_map(fn (array $row): string => implode(' ', $row), $sheets['Petunjuk'])))->toContain('8')->toContain('100');
});

it('creates resorts from the file and says how many', function () {
    importResorts(workbook([['Kode', 'Nama'], ['r1', ' north '], ['R2', 'South']]))
        ->assertSessionHasNoErrors()
        ->assertSessionHas('success', '2 resort dibuat, 0 diperbarui, 0 tidak berubah.');

    expect(Institution::query()->orderBy('code')->get(['code', 'name'])->toArray())
        ->toBe([['code' => 'R1', 'name' => 'NORTH'], ['code' => 'R2', 'name' => 'SOUTH']]);
});

it('renames an existing code in place and leaves an equal row alone', function () {
    $kept = Institution::create(['code' => 'R1', 'name' => 'SAME']);
    $renamed = Institution::create(['code' => 'R2', 'name' => 'OLD']);
    AuditLog::query()->getQuery()->delete();

    importResorts(workbook([['Kode', 'Nama'], ['R1', 'same'], ['R2', 'New'], ['R3', 'Third']]))
        ->assertSessionHas('success', '1 resort dibuat, 1 diperbarui, 1 tidak berubah.');

    expect($renamed->fresh()->name)->toBe('NEW')
        ->and($renamed->fresh()->id)->toBe($renamed->id)
        ->and($kept->fresh()->updated_at->equalTo($kept->updated_at))->toBeTrue()
        ->and(AuditLog::query()->where('event', 'institutions.updated')->count())->toBe(1)
        ->and(AuditLog::query()->where('event', 'institutions.created')->count())->toBe(1);
});

it('keeps the applications of a resort that is renamed by an import', function () {
    $resort = Institution::create(['code' => 'R1', 'name' => 'OLD']);
    $loan = applicationAt($resort);

    importResorts(workbook([['Kode', 'Nama'], ['R1', 'Renamed']]))->assertSessionHas('success');

    expect($loan->fresh()->institution_id)->toBe($resort->id)->and($resort->fresh()->name)->toBe('RENAMED');
});

it('never deletes a resort that is missing from the file', function () {
    Institution::create(['code' => 'R1', 'name' => 'ONE']);

    importResorts(workbook([['Kode', 'Nama'], ['R2', 'Two']]));

    expect(Institution::count())->toBe(2);
});

it('saves nothing and lists every bad row when one row is wrong', function () {
    importResorts(workbook([
        ['Kode', 'Nama'],
        ['OK1', 'Fine'],
        ['', 'No code'],
        ['TOOLONGCODE', 'Long code'],
        ['OK2', str_repeat('x', 101)],
        ['DUP', 'First'],
        ['dup', 'Second'],
        ['OK3', ''],
    ]))->assertSessionHasErrors('file');

    $issues = session('issues');

    expect(Institution::count())->toBe(0)
        ->and($issues)->toHaveCount(5)
        ->and($issues[0])->toStartWith('Baris 3:')
        ->and(implode("\n", $issues))->toContain('Baris 4:')->toContain('Baris 5:')->toContain('Baris 7:')->toContain('Baris 8:');
});

it('refuses a file that is not a workbook of the template', function (UploadedFile $file) {
    importResorts($file)->assertSessionHasErrors('file');

    expect(Institution::count())->toBe(0);
})->with([
    'truncated workbook' => fn () => truncatedWorkbook(),
    'zip without a workbook' => fn () => zipUpload(['notes.txt' => 'x']),
    'workbook parts that are not xml' => fn () => zipUpload(['[Content_Types].xml' => '<<<', 'xl/workbook.xml' => '<<<', 'xl/worksheets/sheet1.xml' => '<<<']),
    'text renamed to xlsx' => fn () => UploadedFile::fake()->createWithContent('resorts.xlsx', 'Kode;Nama'),
    'wrong extension' => fn () => workbook([['Kode', 'Nama'], ['R1', 'A']], 'resorts.csv'),
    'no rows' => fn () => workbook([]),
    'header only' => fn () => workbook([['Kode', 'Nama']]),
    'wrong header' => fn () => workbook([['Nama', 'Kode'], ['A', 'R1']]),
    'data beyond the columns' => fn () => workbook([['Kode', 'Nama'], ['R1', 'A', 'extra']]),
    'extra column' => fn () => workbook([['Kode', 'Nama', 'Lain'], ['R1', 'A', 'x']]),
]);

it('refuses a missing upload and one over the size limit', function () {
    $this->post(route('references.import', 'resorts'), [])->assertSessionHasErrors('file');
    importResorts(UploadedFile::fake()->create('big.xlsx', 2049))->assertSessionHasErrors('file');
});

it('accepts 1000 rows and refuses 1001', function () {
    $rows = fn (int $count): array => [['Kode', 'Nama'], ...array_map(fn (int $i): array => [sprintf('C%05d', $i), "Name $i"], range(1, $count))];

    importResorts(workbook($rows(1001)))->assertSessionHasErrors('file');
    expect(Institution::count())->toBe(0);

    importResorts(workbook($rows(1000)))->assertSessionHasNoErrors();
    expect(Institution::count())->toBe(1000);
});

it('stores a formula-looking cell as the plain text it is and never evaluates it', function () {
    $literal = new Row([new StringCell('F1', null), new StringCell('=1+1', null)]);
    $formula = Row::fromValues(['F2', '=HYPERLINK("http://example.test")']);
    $path = tempnam(sys_get_temp_dir(), 'xlsx');
    $writer = new Writer;
    $writer->openToFile($path);

    foreach ([Row::fromValues(['Kode', 'Nama']), $literal, $formula] as $row) {
        $writer->addRow($row);
    }

    $writer->close();

    importResorts(new UploadedFile($path, 'resorts.xlsx', null, null, true));

    expect(Institution::query()->orderBy('code')->pluck('name', 'code')->all())
        ->toBe(['F1' => '=1+1', 'F2' => '=HYPERLINK("HTTP://EXAMPLE.TEST")']);
});

it('ignores blank lines', function () {
    importResorts(workbook([['Kode', 'Nama'], [null, null], ['R1', 'One'], ['', '']]))->assertSessionHasNoErrors();

    expect(Institution::count())->toBe(1);
});

it('saves nothing when the same code is inserted at the same moment', function () {
    Institution::creating(function (Institution $resort): void {
        if ($resort->code === 'R2') {
            throw new UniqueConstraintViolationException('sqlite', 'insert into institutions', [], new Exception('UNIQUE constraint failed: institutions.code'));
        }
    });

    importResorts(workbook([['Kode', 'Nama'], ['R1', 'One'], ['R2', 'Two']]))->assertSessionHasErrors('file');

    expect(Institution::count())->toBe(0)->and(session('issues'))->toBeNull();
});

it('keeps only Super Admin in: template and import', function () {
    $this->actingAs(userWith(['dashboard.view']));

    $this->get(route('references.import.template', 'resorts'))->assertForbidden();
    importResorts(workbook([['Kode', 'Nama'], ['R1', 'One']]))->assertForbidden();

    expect(Institution::count())->toBe(0);
});

it('offers import only for tables that opt in', function () {
    $this->get(route('references.import.template', 'products'))->assertNotFound();
    $this->post(route('references.import', 'products'), ['file' => workbook([['Kode', 'Nama'], ['R1', 'One']])])->assertNotFound();
});

it('writes one summary entry for an import and one for a refused import', function () {
    AuditLog::query()->getQuery()->delete();

    importResorts(workbook([['Kode', 'Nama'], ['R1', 'One']]));
    importResorts(workbook([['Kode', 'Nama'], ['', 'Nothing']]));

    $done = AuditLog::query()->where('event', 'references.imported')->where('outcome', 'success')->firstOrFail();
    $refused = AuditLog::query()->where('event', 'references.imported')->where('outcome', 'failure')->firstOrFail();

    expect(json_decode((string) $done->new_values, true))->toMatchArray(['created' => 1, 'updated' => 0, 'unchanged' => 0])
        ->and(json_decode((string) $done->context, true))->toMatchArray(['resource' => 'resorts'])
        ->and(json_decode((string) $refused->context, true))->toMatchArray(['resource' => 'resorts', 'issues' => 1])
        ->and(AuditLog::query()->where('event', 'institutions.created')->count())->toBe(1);
});

it('refuses to delete a resort that applications use, in the app and in the database', function () {
    $resort = Institution::create(['code' => 'R1', 'name' => 'ONE']);
    applicationAt($resort);

    $this->delete(route('references.destroy', ['resorts', $resort->id]))->assertSessionHas('error');
    expect(Institution::count())->toBe(1);

    expect(fn () => DB::table('institutions')->where('id', $resort->id)->delete())->toThrow(QueryException::class);

    LoanApplication::query()->forceDelete();
    $this->delete(route('references.destroy', ['resorts', $resort->id]))->assertSessionHas('success');
});

it('hands the page the import urls only for tables that opt in', function () {
    $this->get(route('references.index', 'resorts'))->assertInertia(fn ($page) => $page
        ->where('resource.import.url', '/references/resorts/import')
        ->where('resource.import.template_url', '/references/resorts/import/template'));

    $this->get(route('references.index', 'products'))->assertInertia(fn ($page) => $page->where('resource.import', null));
});
