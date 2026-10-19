<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

/**
 * The living reference of every shared UI component and colour token (.ai/rules/js.md). Static examples only, no data.
 */
class UiGuidelineController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('ui-guideline');
    }
}
