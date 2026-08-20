<?php

namespace App\Support;

use App\Models\SchemaDraft;
use App\Models\SchemaDraftColumn;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Alat developer: membandingkan rancangan tabel dengan skema database nyata
 * dan menyusun pratinjau kode migration. TIDAK pernah mengubah skema.
 */
class SchemaDesign
{
    /** Tipe kolom yang didukung pratinjau. */
    public const TYPES = [
        'string', 'char', 'text', 'longText', 'integer', 'bigInteger', 'unsignedBigInteger',
        'tinyInteger', 'unsignedTinyInteger', 'smallInteger', 'boolean', 'decimal', 'float',
        'date', 'dateTime', 'time', 'timestamp', 'json', 'uuid', 'foreignId',
    ];

    /** Padanan tipe rancangan ke tipe fisik yang dianggap cocok. */
    private const TYPE_MATCH = [
        'string' => ['varchar', 'char', 'text'],
        'char' => ['varchar', 'char'],
        'text' => ['text', 'clob'],
        'longText' => ['text', 'clob'],
        'integer' => ['integer', 'int', 'bigint'],
        'bigInteger' => ['integer', 'bigint'],
        'unsignedBigInteger' => ['integer', 'bigint'],
        'tinyInteger' => ['integer', 'tinyint'],
        'unsignedTinyInteger' => ['integer', 'tinyint'],
        'smallInteger' => ['integer', 'smallint'],
        'boolean' => ['tinyint(1)', 'tinyint', 'boolean', 'integer'],
        'decimal' => ['numeric', 'decimal'],
        'float' => ['float', 'real', 'double'],
        'date' => ['date'],
        'dateTime' => ['datetime', 'timestamp'],
        'time' => ['time'],
        'timestamp' => ['datetime', 'timestamp'],
        'json' => ['text', 'json'],
        'uuid' => ['varchar', 'char'],
        'foreignId' => ['integer', 'bigint'],
    ];

    /** Kolom yang diurus flag rancangan, bukan daftar kolom. */
    private const RESERVED = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public static function tableExists(SchemaDraft $draft): bool
    {
        return Schema::hasTable($draft->table_name);
    }

    /**
     * Perbandingan rancangan vs skema nyata.
     * status: baru | sama | berubah | dihapus.
     */
    public static function diff(SchemaDraft $draft): array
    {
        if (! self::tableExists($draft)) {
            return $draft->columns->map(fn (SchemaDraftColumn $c) => [
                'name' => $c->name,
                'status' => 'baru',
                'draft' => self::draftSignature($c),
                'actual' => null,
                'note' => 'Tabel belum ada di database.',
            ])->all();
        }

        $actual = collect(Schema::getColumns($draft->table_name))->keyBy('name');
        $rows = [];

        foreach ($draft->columns as $column) {
            $real = $actual->get($column->name);

            if (! $real) {
                $rows[] = [
                    'name' => $column->name,
                    'status' => 'baru',
                    'draft' => self::draftSignature($column),
                    'actual' => null,
                    'note' => 'Belum ada di database.',
                ];

                continue;
            }

            $notes = [];
            $allowed = self::TYPE_MATCH[$column->type] ?? [];
            $realType = Str::lower($real['type_name'] ?? $real['type']);

            if ($allowed && ! in_array($realType, $allowed, true)) {
                $notes[] = "tipe {$realType} → {$column->type}";
            }

            if ((bool) $real['nullable'] !== $column->is_nullable) {
                $notes[] = $column->is_nullable ? 'jadi nullable' : 'jadi wajib (NOT NULL)';
            }

            $rows[] = [
                'name' => $column->name,
                'status' => $notes ? 'berubah' : 'sama',
                'draft' => self::draftSignature($column),
                'actual' => self::actualSignature($real),
                'note' => implode(', ', $notes),
            ];
        }

        foreach ($actual as $name => $real) {
            if (self::isReserved($name, $draft) || $draft->columns->contains('name', $name)) {
                continue;
            }

            $rows[] = [
                'name' => $name,
                'status' => 'dihapus',
                'draft' => null,
                'actual' => self::actualSignature($real),
                'note' => 'Ada di database tapi tidak ada di rancangan.',
            ];
        }

        return $rows;
    }

    /** Pratinjau kode migration: create bila tabel belum ada, table bila sudah ada. */
    public static function migrationCode(SchemaDraft $draft): string
    {
        $exists = self::tableExists($draft);
        $body = $exists ? self::alterBody($draft) : self::createBody($draft);
        $method = $exists ? 'table' : 'create';
        $down = $exists
            ? '        // Sesuaikan pembatalan sesuai perubahan di atas.'
            : "        Schema::dropIfExists('{$draft->table_name}');";

        return <<<PHP
        <?php

        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        /** {$draft->name} */
        return new class extends Migration
        {
            public function up(): void
            {
                Schema::{$method}('{$draft->table_name}', function (Blueprint \$table) {
        {$body}
                });
            }

            public function down(): void
            {
        {$down}
            }
        };
        PHP;
    }

    /** Nama file migration yang disarankan. */
    public static function fileName(SchemaDraft $draft): string
    {
        $action = self::tableExists($draft) ? 'update' : 'create';

        return now()->format('Y_m_d_His')."_{$action}_{$draft->table_name}_table.php";
    }

    /** Kolom hasil impor dari tabel nyata (untuk mengisi rancangan). */
    public static function importFrom(string $table): array
    {
        $rows = [];
        $sort = 0;

        foreach (Schema::getColumns($table) as $column) {
            if (in_array($column['name'], self::RESERVED, true)) {
                continue;
            }

            $rows[] = [
                'sort' => $sort++,
                'name' => $column['name'],
                'type' => self::guessType(Str::lower($column['type_name'] ?? $column['type'])),
                'length' => null,
                'is_nullable' => (bool) $column['nullable'],
                'default_value' => self::cleanDefault($column['default']),
                'is_unique' => false,
                'is_index' => false,
                'foreign_table' => null,
                'comment' => $column['comment'] ?? null,
            ];
        }

        return $rows;
    }

    private static function isReserved(string $name, SchemaDraft $draft): bool
    {
        return match ($name) {
            'id' => $draft->with_id,
            'created_at', 'updated_at' => $draft->with_timestamps,
            'deleted_at' => $draft->with_soft_deletes,
            default => false,
        };
    }

    private static function createBody(SchemaDraft $draft): string
    {
        $lines = [];

        if ($draft->with_id) {
            $lines[] = '$table->id();';
        }

        foreach ($draft->columns as $column) {
            $lines[] = self::columnLine($column);
        }

        if ($draft->with_timestamps) {
            $lines[] = '$table->timestamps();';
        }

        if ($draft->with_soft_deletes) {
            $lines[] = '$table->softDeletes();';
        }

        return self::indent($lines);
    }

    private static function alterBody(SchemaDraft $draft): string
    {
        $diff = collect(self::diff($draft));
        $lines = [];

        $new = $diff->where('status', 'baru');
        if ($new->isNotEmpty()) {
            $lines[] = '// Kolom baru';
            foreach ($new as $row) {
                $lines[] = self::columnLine($draft->columns->firstWhere('name', $row['name']));
            }
        }

        $changed = $diff->where('status', 'berubah');
        if ($changed->isNotEmpty()) {
            $lines[] = $lines ? '' : null;
            $lines[] = '// Kolom berubah';
            foreach ($changed as $row) {
                $lines[] = rtrim(self::columnLine($draft->columns->firstWhere('name', $row['name'])), ';').'->change();';
            }
        }

        $removed = $diff->where('status', 'dihapus')->pluck('name');
        if ($removed->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '// Kolom dihapus';
            $list = $removed->map(fn ($n) => "'{$n}'")->implode(', ');
            $lines[] = "\$table->dropColumn([{$list}]);";
        }

        return self::indent(array_values(array_filter($lines, fn ($l) => $l !== null)))
            ?: '            //';
    }

    private static function columnLine(SchemaDraftColumn $column): string
    {
        if ($column->type === 'foreignId' && $column->foreign_table) {
            $line = "\$table->foreignId('{$column->name}')";
            $line .= $column->is_nullable ? '->nullable()' : '';

            return $line."->constrained('{$column->foreign_table}')->cascadeOnDelete();";
        }

        $args = "'{$column->name}'";

        if (filled($column->length)) {
            $args .= ', '.$column->length;
        }

        $line = "\$table->{$column->type}({$args})";
        $line .= $column->is_nullable ? '->nullable()' : '';

        if (filled($column->default_value)) {
            $default = is_numeric($column->default_value)
                ? $column->default_value
                : "'{$column->default_value}'";
            $line .= "->default({$default})";
        }

        $line .= $column->is_unique ? '->unique()' : '';
        $line .= ! $column->is_unique && $column->is_index ? '->index()' : '';
        $line .= filled($column->comment) ? "->comment('{$column->comment}')" : '';

        return $line.';';
    }

    private static function indent(array $lines): string
    {
        return collect($lines)
            ->map(fn ($line) => $line === '' ? '' : '            '.$line)
            ->implode("\n");
    }

    private static function draftSignature(SchemaDraftColumn $c): string
    {
        $parts = [$c->type.(filled($c->length) ? "({$c->length})" : '')];
        $parts[] = $c->is_nullable ? 'nullable' : 'not null';

        if (filled($c->default_value)) {
            $parts[] = "default {$c->default_value}";
        }

        if ($c->is_unique) {
            $parts[] = 'unique';
        }

        if ($c->is_index) {
            $parts[] = 'index';
        }

        if (filled($c->foreign_table)) {
            $parts[] = "→ {$c->foreign_table}";
        }

        return implode(' · ', $parts);
    }

    private static function actualSignature(array $real): string
    {
        $parts = [$real['type'] ?? $real['type_name']];
        $parts[] = $real['nullable'] ? 'nullable' : 'not null';

        if (filled($real['default'])) {
            $parts[] = 'default '.self::cleanDefault($real['default']);
        }

        return implode(' · ', $parts);
    }

    private static function cleanDefault(?string $value): ?string
    {
        return $value === null ? null : trim($value, "'\"");
    }

    private static function guessType(string $sqlType): string
    {
        foreach (self::TYPE_MATCH as $type => $matches) {
            if (in_array($sqlType, $matches, true) && in_array($type, ['string', 'text', 'integer', 'decimal', 'date', 'dateTime', 'time', 'boolean', 'float'], true)) {
                return $type;
            }
        }

        return 'string';
    }
}
