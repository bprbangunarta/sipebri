<?php

namespace App\Http\Controllers;

use App\Models\Institution;

class InstitutionController extends ReferenceController
{
    protected function model(): string
    {
        return Institution::class;
    }

    protected function slug(): string
    {
        return 'institutions';
    }

    protected function label(): string
    {
        return 'Data Instansi';
    }
}
