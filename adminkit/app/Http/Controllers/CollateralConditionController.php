<?php

namespace App\Http\Controllers;

use App\Models\CollateralCondition;

class CollateralConditionController extends ReferenceController
{
    protected function model(): string
    {
        return CollateralCondition::class;
    }

    protected function slug(): string
    {
        return 'collateral-conditions';
    }

    protected function label(): string
    {
        return 'Kondisi Agunan';
    }
}
