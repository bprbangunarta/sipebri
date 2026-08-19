<?php

namespace App\Http\Controllers;

use App\Models\Method;

class MethodController extends ReferenceController
{
    protected function model(): string
    {
        return Method::class;
    }

    protected function slug(): string
    {
        return 'methods';
    }

    protected function label(): string
    {
        return 'Sistem Bunga';
    }
}
