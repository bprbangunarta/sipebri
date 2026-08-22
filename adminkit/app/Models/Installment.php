<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Sistem cicilan. `period_months` = kelipatan jangka waktu (0 = non angsuran). */
class Installment extends Model
{
    protected $fillable = ['code', 'name', 'period_months'];

    protected $casts = ['period_months' => 'integer'];
}
