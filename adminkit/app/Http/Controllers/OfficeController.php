<?php

namespace App\Http\Controllers;

use App\Models\Office;

class OfficeController extends ReferenceController
{
    protected function model(): string
    {
        return Office::class;
    }

    protected function slug(): string
    {
        return 'offices';
    }

    protected function label(): string
    {
        return 'Data Kantor';
    }

    protected function fields(): array
    {
        return [
            ['key' => 'code', 'label' => 'Kode', 'uppercase' => true, 'unique' => true],
            ['key' => 'alias', 'label' => 'Alias', 'uppercase' => true, 'unique' => true, 'hide_below' => 'sm'],
            ['key' => 'name', 'label' => 'Nama'],
        ];
    }
}
