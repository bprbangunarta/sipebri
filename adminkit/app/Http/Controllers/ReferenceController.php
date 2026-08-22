<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reference\BulkReferenceRequest;
use App\Http\Requests\Reference\StoreReferenceRequest;
use App\Models\ActivityLog;
use App\Support\TableQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Basis CRUD data referensi sederhana (kode + nama). Seluruh turunan memakai
 * satu halaman Vue (`pages/Reference.vue`) dan hanya mendefinisikan konfigurasi.
 * Penghapusan bersifat permanen — data referensi tidak memakai arsip.
 */
abstract class ReferenceController extends Controller
{
    /** @return class-string<Model> */
    abstract protected function model(): string;

    /** Slug rute & kunci modul izin, mis. `institutions`. */
    abstract protected function slug(): string;

    /** Judul halaman, mis. `Data Instansi`. */
    abstract protected function label(): string;

    /**
     * Kolom yang dikelola.
     *
     * @return array<int, array{key: string, label: string, uppercase?: bool}>
     */
    protected function fields(): array
    {
        return [
            ['key' => 'code', 'label' => 'Kode', 'uppercase' => true, 'unique' => true],
            ['key' => 'name', 'label' => 'Nama'],
        ];
    }

    public function index(Request $request): Response
    {
        $keys = collect($this->fields())->pluck('key')->all();
        $textKeys = collect($this->fields())
            ->whereNotIn('type', ['boolean', 'number'])
            ->pluck('key')->all();
        $flag = collect($this->fields())->firstWhere('type', 'boolean')['key'] ?? null;
        $search = TableQuery::search($request);
        $sort = TableQuery::sort($request, $keys, $keys[0]);
        $dir = TableQuery::direction($request);
        $status = (string) $request->input('status', '');

        $records = $this->model()::query()
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($textKeys, $search) {
                foreach ($textKeys as $key) {
                    $w->orWhere($key, 'like', "%{$search}%");
                }
            }))
            ->when($flag && in_array($status, ['active', 'inactive'], true),
                fn ($q) => $q->where($flag, $status === 'active' ? 1 : 0))
            ->orderBy($sort, $dir)
            ->paginate(TableQuery::perPage($request))
            ->withQueryString();

        return Inertia::render('Reference', [
            'title' => $this->label(),
            'slug' => $this->slug(),
            'module' => $this->slug(),
            'fields' => $this->fields(),
            'records' => [
                'data' => collect($records->items())
                    ->map(fn (Model $m) => ['id' => $m->getKey(), ...$m->only($keys)])
                    ->all(),
                'meta' => TableQuery::meta($records),
            ],
            'filters' => ['search' => $search, 'sort' => $sort, 'dir' => $dir, 'status' => $status],
            'hasStatus' => (bool) $flag,
        ]);
    }

    public function store(StoreReferenceRequest $request): RedirectResponse
    {
        $record = $this->model()::create($request->validated());

        ActivityLog::record(
            "Menambah {$this->label()} {$record->code}",
            $this->label(),
            'success',
            $record,
            ActivityLog::snapshotOf($record),
        );

        return back()->with('success', "{$this->label()} {$record->code} ditambahkan.");
    }

    public function update(StoreReferenceRequest $request, int $id): RedirectResponse
    {
        $record = $this->model()::findOrFail($id);
        $before = $record->getOriginal();
        $record->update($request->validated());

        ActivityLog::record(
            "Memperbarui {$this->label()} {$record->code}",
            $this->label(),
            'info',
            $record,
            ActivityLog::diffOf($record, $before),
        );

        return back()->with('success', "{$this->label()} {$record->code} diperbarui.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $record = $this->model()::findOrFail($id);
        $code = $record->code;
        $snapshot = ActivityLog::snapshotOf($record, deleted: true);
        $record->delete();

        ActivityLog::record("Menghapus {$this->label()} {$code}", $this->label(), 'warning', changes: $snapshot);

        return back()->with('success', "{$this->label()} {$code} dihapus.");
    }

    public function bulkDestroy(BulkReferenceRequest $request): RedirectResponse
    {
        $records = $this->model()::whereIn('id', $request->validated()['ids'])->get();

        if ($records->isEmpty()) {
            return back()->with('error', 'Tidak ada data yang dapat dihapus.');
        }

        $count = $records->count();
        $this->model()::whereIn('id', $records->modelKeys())->delete();

        ActivityLog::record(
            "Menghapus {$count} {$this->label()} secara massal",
            $this->label(),
            'warning',
            context: ['kode' => $records->pluck('code')->implode(', ')],
        );

        return back()->with('success', "{$count} data dihapus.");
    }

    /** Aturan validasi dipakai oleh StoreReferenceRequest. Unik hanya untuk kolom bertanda. */
    public function rules(?int $id = null): array
    {
        $table = $this->table();

        return collect($this->fields())
            ->mapWithKeys(fn (array $field) => [
                $field['key'] => match ($field['type'] ?? '') {
                    'boolean' => ['boolean'],
                    'number' => ['required', 'integer', 'min:0', 'max:'.($field['max'] ?? 999)],
                    default => [
                        'required', 'string', 'max:255',
                        ...($field['unique'] ?? false ? [Rule::unique($table, $field['key'])->ignore($id)] : []),
                    ],
                },
            ])
            ->all();
    }

    public function table(): string
    {
        return (new ($this->model()))->getTable();
    }

    /** Nama atribut untuk pesan galat. */
    public function attributeNames(): array
    {
        return collect($this->fields())
            ->mapWithKeys(fn (array $f) => [$f['key'] => mb_strtolower($f['label'])])
            ->all();
    }

    /** Kolom bertipe boolean; nilainya selalu disimpan sebagai 0/1. */
    public function booleanFields(): array
    {
        return collect($this->fields())->where('type', 'boolean')->pluck('key')->all();
    }

    /** Kolom bertipe angka; nilainya selalu disimpan sebagai bilangan bulat. */
    public function numberFields(): array
    {
        return collect($this->fields())->where('type', 'number')->pluck('key')->all();
    }

    /** Kolom yang nilainya selalu disimpan dalam huruf besar. */
    public function uppercaseFields(): array
    {
        return collect($this->fields())->where('uppercase', true)->pluck('key')->all();
    }
}
