<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductParameterRequest extends FormRequest
{
    /** The form always sends every field; one that is left out is empty (a limit that is cleared), never the value that was there before. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            ...array_diff_key(array_fill_keys(['min_amount', 'max_amount', 'min_tenor', 'max_tenor', 'interest_rate', 'provision_rate', 'admin_rate', 'rc_threshold', 'default_method_id', 'default_installment_id', 'decree', 'note'], null), $this->all()),
            ...array_diff_key(['allowed_method_ids' => [], 'allowed_installment_ids' => [], 'collateral_required' => false], $this->all()),
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'min_amount' => ['nullable', 'integer', 'min:0'],
            // Empty means no limit, and so does zero where the parameter is used: a maximum of zero is not a limit, so it is refused.
            'max_amount' => ['nullable', 'integer', 'min:1', 'gte:min_amount'],
            'min_tenor' => ['nullable', 'integer', 'min:1', 'max:600'],
            'max_tenor' => ['nullable', 'integer', 'min:1', 'max:600', 'gte:min_tenor'],
            'interest_rate' => ['nullable', 'decimal:0,3', 'min:0', 'max:100'],
            'provision_rate' => ['nullable', 'decimal:0,3', 'min:0', 'max:100'],
            'admin_rate' => ['nullable', 'decimal:0,3', 'min:0', 'max:100'],
            // Zero reads as "not set" in the approval, so it is not a threshold.
            'rc_threshold' => ['nullable', 'decimal:0,3', 'min:1', 'max:100'],
            'default_method_id' => ['nullable', 'integer', Rule::exists('methods', 'id')],
            'default_installment_id' => ['nullable', 'integer', Rule::exists('installments', 'id')],
            'allowed_method_ids' => ['array'],
            'allowed_method_ids.*' => ['integer', Rule::exists('methods', 'id')],
            'allowed_installment_ids' => ['array'],
            'allowed_installment_ids.*' => ['integer', Rule::exists('installments', 'id')],
            'collateral_required' => ['boolean'],
            'decree' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:255'],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }

    /**
     * A default must be one of the allowed choices (when a restriction is set).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $pairs = [
                'default_method_id' => ['allowed_method_ids', 'Metode bunga bawaan harus salah satu dari metode yang diizinkan.'],
                'default_installment_id' => ['allowed_installment_ids', 'Sistem angsuran bawaan harus salah satu dari sistem yang diizinkan.'],
            ];

            foreach ($pairs as $field => [$allowedField, $message]) {
                $allowed = $this->input($allowedField, []);

                if (filled($this->input($field)) && filled($allowed) && ! in_array((int) $this->input($field), array_map('intval', $allowed), true)) {
                    $validator->errors()->add($field, $message);
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'min_amount' => 'plafon minimum', 'max_amount' => 'plafon maksimum',
            'min_tenor' => 'tenor minimum', 'max_tenor' => 'tenor maksimum', 'interest_rate' => 'suku bunga',
            'provision_rate' => 'provisi', 'admin_rate' => 'biaya administrasi', 'rc_threshold' => 'batas RC',
            'default_method_id' => 'metode bunga bawaan', 'default_installment_id' => 'sistem angsuran bawaan', 'decree' => 'nomor SK Direksi', 'reason' => 'alasan perubahan',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'max_amount.gte' => 'Plafon maksimum tidak boleh lebih kecil dari minimum.',
            'max_tenor.gte' => 'Tenor maksimum tidak boleh lebih kecil dari minimum.',
        ];
    }
}
