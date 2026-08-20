<?php

namespace App\Http\Controllers;

use App\Models\CollateralType;

class CollateralTypeController extends ReferenceController
{
    protected function model(): string
    {
        return CollateralType::class;
    }

    protected function slug(): string
    {
        return 'collateral-types';
    }

    protected function label(): string
    {
        return 'Jenis Agunan';
    }
}
