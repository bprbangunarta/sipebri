<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

/**
 * Committee approval of analysed files. Placeholder: the page exists so the menu and permission are in place,
 * the list and the decisions come with the analysis worksheet.
 */
class ApprovalController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('approvals/index');
    }
}
