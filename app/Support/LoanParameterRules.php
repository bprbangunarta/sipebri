<?php

namespace App\Support;

use App\Models\LoanApplication;
use App\Models\ProductParameter;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * What the parameters of a product (and the BMPK) say about the amount, tenor, method and installment of a loan application.
 * One place, because it is asked twice: when a draft is saved, and again when it is submitted (the parameters may have changed
 * in between).
 */
class LoanParameterRules
{
    /** The strictest of the product maximum and the BMPK (null = no limit). */
    public static function ceiling(?ProductParameter $parameter, ?int $bmpk): ?int
    {
        $limits = array_filter([$parameter?->max_amount ? (int) $parameter->max_amount : null, $bmpk]);

        return $limits === [] ? null : min($limits);
    }

    /**
     * @return array<string, list<mixed>>
     */
    public static function rules(?ProductParameter $parameter, ?int $bmpk): array
    {
        $ceiling = self::ceiling($parameter, $bmpk);

        return [
            'requested_amount' => ['required', 'integer', 'min:'.max(1, (int) $parameter?->min_amount), ...($ceiling ? ['max:'.$ceiling] : [])],
            'requested_tenor' => ['required', 'integer', 'min:'.max(1, (int) $parameter?->min_tenor), 'max:'.($parameter?->max_tenor ?: 600)],
            'method_id' => ['required', 'integer', 'exists:methods,id', ...($parameter?->allowed_method_ids ? [Rule::in($parameter->allowed_method_ids)] : [])],
            'installment_id' => ['required', 'integer', 'exists:installments,id', ...($parameter?->allowed_installment_ids ? [Rule::in($parameter->allowed_installment_ids)] : [])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(?ProductParameter $parameter, ?int $bmpk): array
    {
        $idr = LendingLimit::format(...);
        $productMax = $parameter?->max_amount ? (int) $parameter->max_amount : null;
        // The BMPK message wins when it is the stricter of the two limits.
        $max = $bmpk !== null && ($productMax === null || $bmpk <= $productMax)
            ? ['requested_amount.max' => "Plafon melebihi batas maksimum pemberian kredit (BMPK) sebesar {$idr($bmpk)}."]
            : ($productMax !== null ? ['requested_amount.max' => "Plafon maksimum untuk produk ini adalah {$idr($productMax)}."] : []);

        if (! $parameter) {
            return $max;
        }

        return [
            ...$max,
            'requested_amount.min' => "Plafon minimum untuk produk ini adalah {$idr($parameter->min_amount)}.",
            'requested_tenor.min' => "Tenor minimum untuk produk ini adalah {$parameter->min_tenor} bulan.",
            'requested_tenor.max' => "Tenor maksimum untuk produk ini adalah {$parameter->max_tenor} bulan.",
            'method_id.in' => 'Metode bunga ini tidak diizinkan untuk produk tersebut.',
            'installment_id.in' => 'Sistem angsuran ini tidak diizinkan untuk produk tersebut.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return ['requested_amount' => 'plafon', 'requested_tenor' => 'tenor', 'method_id' => 'metode bunga', 'installment_id' => 'sistem angsuran'];
    }

    /** A saved draft against the parameters as they are now. */
    public static function checkDraft(LoanApplication $loan, ?int $bmpk): ValidatorContract
    {
        $parameter = ProductParameter::query()->where('product_id', $loan->product_id)->first();

        return Validator::make(
            $loan->only(['requested_amount', 'requested_tenor', 'method_id', 'installment_id']),
            self::rules($parameter, $bmpk),
            self::messages($parameter, $bmpk),
            self::attributes(),
        );
    }
}
