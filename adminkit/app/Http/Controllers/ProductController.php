<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreParameterRequest;
use App\Models\ActivityLog;
use App\Models\Installment;
use App\Models\Method;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends ReferenceController
{
    protected function model(): string
    {
        return Product::class;
    }

    protected function slug(): string
    {
        return 'products';
    }

    protected function label(): string
    {
        return 'Data Produk';
    }

    protected function fields(): array
    {
        return [
            ['key' => 'code', 'label' => 'Kode', 'uppercase' => true, 'unique' => true],
            ['key' => 'alias', 'label' => 'Alias', 'uppercase' => true, 'unique' => true, 'hide_below' => 'sm'],
            ['key' => 'name', 'label' => 'Nama'],
            ['key' => 'is_active', 'label' => 'Status', 'type' => 'boolean'],
        ];
    }

    /** Detail produk berisi parameter SK Direksi. */
    public function show(int $id): Response
    {
        $product = Product::with('parameter')->findOrFail($id);

        return Inertia::render('ProductDetail', [
            'product' => $product->only(['id', 'code', 'alias', 'name']),
            'parameter' => $product->parameter?->only([
                'min_amount', 'max_amount', 'min_tenor', 'max_tenor',
                'interest_rate', 'provision_rate', 'admin_rate', 'rc_threshold',
                'default_method_id', 'default_installment_id',
                'allowed_method_ids', 'allowed_installment_ids',
                'collateral_required', 'decree', 'note',
            ]),
            'methodOptions' => Method::orderBy('code')->get()
                ->map(fn (Method $m) => ['value' => $m->id, 'label' => $m->name])->all(),
            'installmentOptions' => Installment::orderBy('code')->get()
                ->map(fn (Installment $i) => ['value' => $i->id, 'label' => $i->name])->all(),
        ]);
    }

    public function updateParameter(StoreParameterRequest $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $parameter = $product->parameter;
        $before = $parameter?->getOriginal() ?? [];

        $parameter = $product->parameter()->updateOrCreate([], $request->validated());

        ActivityLog::record(
            "Memperbarui parameter produk {$product->alias}",
            'Data Produk',
            'info',
            $parameter,
            $before === [] ? ActivityLog::snapshotOf($parameter) : ActivityLog::diffOf($parameter, $before),
        );

        return back()->with('success', "Parameter produk {$product->alias} disimpan.");
    }
}
