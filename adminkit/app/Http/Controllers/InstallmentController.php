<?php

namespace App\Http\Controllers;

use App\Models\Installment;

class InstallmentController extends ReferenceController
{
    protected function model(): string
    {
        return Installment::class;
    }

    protected function slug(): string
    {
        return 'installments';
    }

    protected function label(): string
    {
        return 'Sistem Cicilan';
    }
}
