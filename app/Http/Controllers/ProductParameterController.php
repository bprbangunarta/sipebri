<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Http\Requests\ProductParameterRequest;
use App\Models\Installment;
use App\Models\Method;
use App\Models\Product;
use App\Models\ProductParameter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Board-decree parameters per product. They are references that loan applications are checked against.
 */
class ProductParameterController extends Controller
{
    public function show(Request $request, Product $product): Response
    {
        return Inertia::render('references/product-parameters', [
            'product' => $product->only(['id', 'code', 'alias', 'name']),
            'parameter' => $product->parameter?->only([
                'min_amount', 'max_amount', 'min_tenor', 'max_tenor', 'interest_rate', 'provision_rate', 'admin_rate', 'rc_threshold',
                'default_method_id', 'default_installment_id', 'allowed_method_ids', 'allowed_installment_ids',
                'collateral_required', 'decree', 'note',
            ]),
            'methods' => Method::orderBy('code')->get(['id', 'name']),
            'installments' => Installment::orderBy('code')->get(['id', 'name']),
            'canManage' => true,
        ]);
    }

    public function update(ProductParameterRequest $request, Product $product): RedirectResponse
    {
        $reason = trim($request->validated('reason'));
        $values = $request->safe()->except('reason');
        $parameter = $product->parameter ?? new ProductParameter(['product_id' => $product->id]);
        $parameter->fill($values);

        if ($parameter->exists && ! $parameter->isDirty()) {
            return back()->with('success', "Parameter {$product->alias} tidak berubah.");
        }

        // These are board-decree figures: the entry that records the change carries the reason next to the values before and after.
        Audit::withContext(['reason' => $reason], (string) $request->user()->name, fn () => $parameter->save());

        return back()->with('success', "Parameter {$product->alias} berhasil disimpan.");
    }
}
