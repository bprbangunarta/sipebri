<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

/** Simulasi agunan — masih kerangka (placeholder). */
class SimulationController extends Controller
{
    public function collateral(): Response
    {
        return Inertia::render('CollateralSimulation');
    }
}
