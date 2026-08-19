<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommitteePath extends Model
{
    public const MECHANISMS = [
        'plafon' => 'Kewenangan Plafon',
        'hierarki' => 'Hierarki Komite',
    ];

    protected $fillable = ['product_id', 'condition', 'mechanism', 'is_active', 'note'];

    protected $casts = ['is_active' => 'boolean'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(CommitteeTier::class)->orderBy('sort')->orderBy('id');
    }

    /** Judul jalur, mis. "KRU · Reloan" atau "Semua Produk · Normal". */
    public function title(): string
    {
        $product = $this->product?->alias ?? 'Semua Produk';

        return $product.' · '.$this->conditionLabel();
    }

    /** Kondisi disimpan HURUF BESAR, ditampilkan Capitalize. */
    public function conditionLabel(): string
    {
        return $this->condition ? str($this->condition)->lower()->title()->value() : 'Normal';
    }
}
