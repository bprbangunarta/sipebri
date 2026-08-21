<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductParameter;
use Illuminate\Database\Seeder;

/**
 * Parameter produk (acuan SK Direksi) — dipakai form Pengajuan Kredit untuk
 * membatasi plafon, jangka waktu, sistem bunga, sistem cicilan, dan wajib agunan.
 * Cerminan data staging per 22/06/2026.
 */
class ProductParameterSeeder extends Seeder
{
    /** [kode produk, plafon min, plafon maks, tenor min, tenor maks, bunga, provisi, admin, ambang RC, sistem bunga, sistem cicilan, agunan wajib, SK] */
    private const PARAMETERS = [
        ['01', 1000000, 999999999999, 1, 300, 3.5, 0, 0, 70, [1], [3], true, null],
        ['02', 1000000, 999999999999, 1, 60, 13, 0, 0, 70, [1], [3], true, null],
        ['03', 1000000, 999999999999, 1, 36, 15, 0, 0, 70, [1], [3], true, null],
        ['04', 1000000, 999999999999, 1, 36, 14, 0, 0, 70, [4], [8], true, null],
        ['05', 10000000, 999999999999, 6, 60, 13, 0, 0, 70, [1], [3], true, null],
        ['06', 1000000, 150000000, 6, 120, 10, 0, 0, 70, [6], [7], true, null],
        ['07', 1000000, 100000000, 1, 36, 32, 0, 0, 70, [8], [3], true, null],
        ['08', 1000000, 21000000, 6, 60, 13, 0, 0, 70, [1], [3], true, null],
        ['09', 10000000, 100000000, 12, 60, 12, 0, 0, 70, [1], [8], true, null],
        ['10', 1000000, 100000000, 1, 36, 25, 0, 0, 70, [1], [3], true, null],
        ['11', 10000000, 100000000, 1, 3, 18, 0, 0, 70, [1], [3], true, null],
        ['12', 1000000, 250000000, 1, 60, 17.4, 0, 0, 70, [1], [3], true, null],
        ['13', 10000000, 100000000, 12, 36, 12, 0, 0, 70, [1], [8], true, null],
        ['14', 2000000, 10000000, 3, 10, 32, 0, 0, 70, [1], [3], false, null],
        ['15', 1000000, 100000000, 6, 18, 24, 0, 0, 70, [1], [3], true, null],
        ['16', 2000000, 250000000, 6, 120, 14, 0, 0, 70, [1], [3], true, null],
        ['17', 1000000, 3000000, 6, 12, 9, 0, 0, 70, [1], [2], false, null],
    ];

    public function run(): void
    {
        $codes = Product::pluck('id', 'code');

        foreach (self::PARAMETERS as [$code, $minAmount, $maxAmount, $minTenor, $maxTenor,
            $interest, $provision, $admin, $rc, $methods, $installments, $collateral, $decree]) {
            if (! $codes->has($code)) {
                continue;
            }

            ProductParameter::updateOrCreate(['product_id' => $codes[$code]], [
                'min_amount' => $minAmount,
                'max_amount' => $maxAmount,
                'min_tenor' => $minTenor,
                'max_tenor' => $maxTenor,
                'interest_rate' => $interest,
                'provision_rate' => $provision,
                'admin_rate' => $admin,
                'rc_threshold' => $rc,
                'allowed_method_ids' => $methods,
                'allowed_installment_ids' => $installments,
                'collateral_required' => (int) $collateral,
                'decree' => $decree,
            ]);
        }
    }
}
