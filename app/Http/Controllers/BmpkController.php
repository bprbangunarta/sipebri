<?php

namespace App\Http\Controllers;

use App\Support\LendingLimit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The BMPK (legal lending limit). Loan applications above it are refused, whatever the product allows.
 */
class BmpkController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('master-data/bmpk', ['amount' => LendingLimit::bmpk()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(['amount' => ['required', 'integer', 'min:1', 'max:999999999999']], [], ['amount' => 'BMPK']);

        LendingLimit::setBmpk((int) $data['amount']);

        return back()->with('success', 'BMPK berhasil disimpan.');
    }
}
