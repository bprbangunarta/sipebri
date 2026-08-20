<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SchemaDraft;
use App\Models\SchemaDraftColumn;
use App\Support\SchemaDesign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Skema Migrasi — alat developer untuk merancang struktur tabel sebelum
 * migration sesungguhnya dibuat. Modul ini TIDAK pernah mengubah skema database.
 */
class SchemaDraftController extends Controller
{
    private const LABEL = 'Skema Migrasi';

    public function index(): Response
    {
        $drafts = SchemaDraft::withCount('columns')->orderBy('name')->get()
            ->map(fn (SchemaDraft $d) => [
                ...$d->only(['id', 'name', 'table_name', 'note', 'with_id', 'with_timestamps', 'with_soft_deletes']),
                'columns_count' => $d->columns_count,
                'table_exists' => Schema::hasTable($d->table_name),
            ])->all();

        return Inertia::render('SchemaDrafts', ['drafts' => $drafts]);
    }

    public function show(SchemaDraft $schemaDraft): Response
    {
        $schemaDraft->load('columns');

        return Inertia::render('SchemaDraftDetail', [
            'draft' => [
                ...$schemaDraft->only(['id', 'name', 'table_name', 'note', 'with_id', 'with_timestamps', 'with_soft_deletes']),
                'table_exists' => SchemaDesign::tableExists($schemaDraft),
            ],
            'columns' => $schemaDraft->columns->map(fn (SchemaDraftColumn $c) => $c->only([
                'id', 'sort', 'name', 'type', 'length', 'is_nullable', 'default_value',
                'is_unique', 'is_index', 'foreign_table', 'comment',
            ]))->all(),
            'diff' => SchemaDesign::diff($schemaDraft),
            'migration' => [
                'file' => SchemaDesign::fileName($schemaDraft),
                'code' => SchemaDesign::migrationCode($schemaDraft),
            ],
            'types' => SchemaDesign::TYPES,
            'tableOptions' => collect(Schema::getTableListing())->sort()->values()->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $draft = SchemaDraft::create($this->draftData($request));

        ActivityLog::record("Menambah rancangan tabel {$draft->table_name}", self::LABEL, 'success', $draft);

        return to_route('schema-drafts.show', $draft)->with('success', "Rancangan {$draft->table_name} dibuat.");
    }

    public function update(Request $request, SchemaDraft $schemaDraft): RedirectResponse
    {
        $schemaDraft->update($this->draftData($request, $schemaDraft));

        ActivityLog::record("Memperbarui rancangan tabel {$schemaDraft->table_name}", self::LABEL, 'info', $schemaDraft);

        return back()->with('success', "Rancangan {$schemaDraft->table_name} diperbarui.");
    }

    public function destroy(SchemaDraft $schemaDraft): RedirectResponse
    {
        $name = $schemaDraft->table_name;
        $schemaDraft->delete();

        ActivityLog::record("Menghapus rancangan tabel {$name}", self::LABEL, 'warning');

        return to_route('schema-drafts.index')->with('success', "Rancangan {$name} dihapus.");
    }

    public function storeColumn(Request $request, SchemaDraft $schemaDraft): RedirectResponse
    {
        $data = $this->columnData($request, $schemaDraft);
        $data['sort'] = (int) $schemaDraft->columns()->max('sort') + 1;
        $schemaDraft->columns()->create($data);

        return back()->with('success', "Kolom {$data['name']} ditambahkan.");
    }

    public function updateColumn(Request $request, SchemaDraft $schemaDraft, SchemaDraftColumn $column): RedirectResponse
    {
        $column->update($this->columnData($request, $schemaDraft, $column));

        return back()->with('success', "Kolom {$column->name} diperbarui.");
    }

    public function destroyColumn(SchemaDraft $schemaDraft, SchemaDraftColumn $column): RedirectResponse
    {
        $name = $column->name;
        $column->delete();

        return back()->with('success', "Kolom {$name} dihapus.");
    }

    /** Simpan ulang urutan kolom hasil geser. */
    public function reorder(Request $request, SchemaDraft $schemaDraft): RedirectResponse
    {
        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', Rule::exists('schema_draft_columns', 'id')->where('schema_draft_id', $schemaDraft->id)],
        ])['ids'];

        foreach ($ids as $index => $id) {
            SchemaDraftColumn::where('id', $id)->update(['sort' => $index]);
        }

        return back()->with('success', 'Urutan kolom disimpan.');
    }

    /** Isi rancangan dari struktur tabel yang sudah ada di database. */
    public function import(SchemaDraft $schemaDraft): RedirectResponse
    {
        if (! SchemaDesign::tableExists($schemaDraft)) {
            return back()->with('error', "Tabel {$schemaDraft->table_name} belum ada di database.");
        }

        $schemaDraft->columns()->delete();
        $schemaDraft->columns()->createMany(SchemaDesign::importFrom($schemaDraft->table_name));
        $schemaDraft->update([
            'with_id' => Schema::hasColumn($schemaDraft->table_name, 'id'),
            'with_timestamps' => Schema::hasColumn($schemaDraft->table_name, 'created_at'),
            'with_soft_deletes' => Schema::hasColumn($schemaDraft->table_name, 'deleted_at'),
        ]);

        return back()->with('success', "Kolom diimpor dari tabel {$schemaDraft->table_name}.");
    }

    private function draftData(Request $request, ?SchemaDraft $current = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'table_name' => [
                'required', 'string', 'max:64', 'regex:/^[a-z][a-z0-9_]*$/',
                Rule::unique('schema_drafts', 'table_name')->ignore($current?->id),
            ],
            'note' => ['nullable', 'string', 'max:255'],
            'with_id' => ['boolean'],
            'with_timestamps' => ['boolean'],
            'with_soft_deletes' => ['boolean'],
        ], [
            'table_name.regex' => 'Nama tabel hanya boleh huruf kecil, angka, dan garis bawah.',
        ], [
            'name' => 'nama rancangan',
            'table_name' => 'nama tabel',
        ]);
    }

    private function columnData(Request $request, SchemaDraft $draft, ?SchemaDraftColumn $current = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:64', 'regex:/^[a-z][a-z0-9_]*$/',
                Rule::unique('schema_draft_columns', 'name')
                    ->where('schema_draft_id', $draft->id)
                    ->ignore($current?->id),
            ],
            'type' => ['required', 'string', Rule::in(SchemaDesign::TYPES)],
            'length' => ['nullable', 'string', 'max:20'],
            'is_nullable' => ['boolean'],
            'default_value' => ['nullable', 'string', 'max:100'],
            'is_unique' => ['boolean'],
            'is_index' => ['boolean'],
            'foreign_table' => ['nullable', 'string', 'max:64'],
            'comment' => ['nullable', 'string', 'max:255'],
        ], [
            'name.regex' => 'Nama kolom hanya boleh huruf kecil, angka, dan garis bawah.',
        ], [
            'name' => 'nama kolom',
            'type' => 'tipe',
        ]);
    }
}
