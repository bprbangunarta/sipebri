<?php

namespace App\Http\Controllers;

use App\Models\BindingType;

class BindingTypeController extends ReferenceController
{
    protected function model(): string
    {
        return BindingType::class;
    }

    protected function slug(): string
    {
        return 'collateral-bindings';
    }

    protected function label(): string
    {
        return 'Jenis Pengikatan';
    }
}
