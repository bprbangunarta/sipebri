<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends ReferenceController
{
    protected function model(): string
    {
        return Product::class;
    }

    protected function slug(): string
    {
        return 'products';
    }

    protected function label(): string
    {
        return 'Data Produk';
    }

    protected function fields(): array
    {
        return [
            ['key' => 'code', 'label' => 'Kode', 'uppercase' => true, 'unique' => true],
            ['key' => 'alias', 'label' => 'Alias', 'uppercase' => true, 'unique' => true],
            ['key' => 'name', 'label' => 'Nama'],
        ];
    }
}
