<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
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
        return Inertia::render('references/bmpk', ['amount' => LendingLimit::bmpk()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [], ['amount' => 'BMPK', 'reason' => 'alasan perubahan']);

        if (LendingLimit::bmpk() === (int) $data['amount']) {
            return back()->with('success', 'BMPK tidak berubah.');
        }

        // The limit is a regulatory figure: the entry that records the change carries the reason next to the value before and after.
        Audit::withContext(['reason' => trim($data['reason'])], (string) $request->user()->name, fn () => LendingLimit::setBmpk((int) $data['amount']));

        return back()->with('success', 'BMPK berhasil disimpan.');
    }
}
