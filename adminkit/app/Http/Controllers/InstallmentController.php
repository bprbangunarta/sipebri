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

    protected function fields(): array
    {
        return [
            ...parent::fields(),
            [
                'key' => 'period_months',
                'label' => 'Kelipatan Jangka Waktu (Bulan)',
                'type' => 'number',
                'max' => 60,
                'hint' => '0 = tanpa setoran berkala (pokok dibayar sekali di akhir).',
            ],
        ];
    }
}
