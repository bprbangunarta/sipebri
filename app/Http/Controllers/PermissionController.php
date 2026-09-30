<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

/**
 * Management of the Spatie permission items. Placeholder for now: the page is an empty index.
 */
class PermissionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('permissions/index');
    }
}
