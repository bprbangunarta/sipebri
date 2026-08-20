<?php

namespace App\Http\Controllers;

use App\Models\CollateralMethod;

class CollateralMethodController extends ReferenceController
{
    protected function model(): string
    {
        return CollateralMethod::class;
    }

    protected function slug(): string
    {
        return 'collateral-methods';
    }

    protected function label(): string
    {
        return 'Metode Hitung';
    }
}
