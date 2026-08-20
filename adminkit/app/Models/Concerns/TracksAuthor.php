<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Kolom audit standar: created_by / updated_by / deleted_by menyimpan NAMA
 * lengkap pengguna (bukan id) agar riwayat tetap terbaca meski user dihapus.
 */
trait TracksAuthor
{
    public static function bootTracksAuthor(): void
    {
        static::creating(function (Model $model): void {
            $model->created_by ??= static::authorName();
        });

        static::updating(function (Model $model): void {
            $model->updated_by = static::authorName();
        });

        static::deleting(function (Model $model): void {
            if (in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
                $model->deleted_by = static::authorName();
                $model->saveQuietly();
            }
        });
    }

    protected static function authorName(): string
    {
        return auth()->user()?->name ?? 'SISTEM';
    }
}
