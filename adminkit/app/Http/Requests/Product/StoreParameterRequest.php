<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreParameterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'min_amount' => ['nullable', 'integer', 'min:0'],
            'max_amount' => ['nullable', 'integer', 'min:0', 'gte:min_amount'],
            'min_tenor' => ['nullable', 'integer', 'min:1', 'max:600'],
            'max_tenor' => ['nullable', 'integer', 'min:1', 'max:600', 'gte:min_tenor'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'provision_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'admin_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rc_threshold' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'default_method_id' => ['nullable', 'integer', Rule::exists('methods', 'id')],
            'default_installment_id' => ['nullable', 'integer', Rule::exists('installments', 'id')],
            'allowed_method_ids' => ['array'],
            'allowed_method_ids.*' => ['integer', Rule::exists('methods', 'id')],
            'allowed_installment_ids' => ['array'],
            'allowed_installment_ids.*' => ['integer', Rule::exists('installments', 'id')],
            'collateral_required' => ['boolean'],
            'decree' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    /** Nilai bawaan harus termasuk pilihan yang diizinkan. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $pairs = [
                'default_method_id' => ['allowed_method_ids', 'Metode bunga bawaan harus termasuk metode yang diizinkan.'],
                'default_installment_id' => ['allowed_installment_ids', 'Pola cicilan bawaan harus termasuk pola yang diizinkan.'],
            ];

            foreach ($pairs as $field => [$allowedField, $message]) {
                $value = $this->input($field);
                $allowed = $this->input($allowedField, []);

                if (filled($value) && filled($allowed) && ! in_array((int) $value, array_map('intval', $allowed), true)) {
                    $validator->errors()->add($field, $message);
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'min_amount' => 'plafon minimal',
            'max_amount' => 'plafon maksimal',
            'min_tenor' => 'tenor minimal',
            'max_tenor' => 'tenor maksimal',
            'interest_rate' => 'suku bunga',
            'provision_rate' => 'provisi',
            'admin_rate' => 'biaya admin',
            'rc_threshold' => 'ambang RC',
            'default_method_id' => 'metode bunga bawaan',
            'default_installment_id' => 'pola cicilan bawaan',
            'decree' => 'nomor SK Direksi',
            'note' => 'catatan',
        ];
    }

    public function messages(): array
    {
        return [
            'max_amount.gte' => 'Plafon maksimal tidak boleh lebih kecil dari plafon minimal.',
            'max_tenor.gte' => 'Tenor maksimal tidak boleh lebih kecil dari tenor minimal.',
        ];
    }
}
