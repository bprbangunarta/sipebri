<?php

namespace App\Http\Requests\Committee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTierRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'min_amount' => ['nullable', 'integer', 'min:0'],
            'max_amount' => ['nullable', 'integer', 'min:0', 'gte:min_amount'],
            'can_escalate' => ['boolean'],
            'can_approve' => ['boolean'],
            'can_cancel' => ['boolean'],
            'can_reject' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'label' => 'nama jenjang',
            'role' => 'peranan pemutus',
            'min_amount' => 'plafon minimal',
            'max_amount' => 'plafon maksimal',
        ];
    }

    public function messages(): array
    {
        return ['max_amount.gte' => 'Plafon maksimal tidak boleh lebih kecil dari plafon minimal.'];
    }
}
