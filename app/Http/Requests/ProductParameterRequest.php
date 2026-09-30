<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductParameterRequest extends FormRequest
{
    /**
     * @return array<string, list<mixed>>
     */
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

    /**
     * A default must be one of the allowed choices (when a restriction is set).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $pairs = [
                'default_method_id' => ['allowed_method_ids', 'The default interest method must be one of the allowed methods.'],
                'default_installment_id' => ['allowed_installment_ids', 'The default installment system must be one of the allowed systems.'],
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
            'min_amount' => 'minimum loan amount', 'max_amount' => 'maximum loan amount',
            'min_tenor' => 'minimum tenor', 'max_tenor' => 'maximum tenor', 'interest_rate' => 'interest rate',
            'provision_rate' => 'provision', 'admin_rate' => 'admin fee', 'rc_threshold' => 'RC threshold',
            'default_method_id' => 'default interest method', 'default_installment_id' => 'default installment system', 'decree' => 'decree number',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'max_amount.gte' => 'The maximum loan amount cannot be lower than the minimum.',
            'max_tenor.gte' => 'The maximum tenor cannot be lower than the minimum.',
        ];
    }
}
