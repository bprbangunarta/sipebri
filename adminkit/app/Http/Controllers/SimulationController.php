<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

/** Simulasi agunan & analisa kredit — masih kerangka (placeholder). */
class SimulationController extends Controller
{
    public function collateral(): Response
    {
        return Inertia::render('CollateralSimulation');
    }

    public function analysis(): Response
    {
        return Inertia::render('AnalysisSimulation');
    }

    public function approval(): Response
    {
        return Inertia::render('ApprovalSimulation');
    }
}
