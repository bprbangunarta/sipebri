<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommitteeTierRequest extends FormRequest
{
    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'min_amount' => ['nullable', 'integer', 'min:0'],
            'max_amount' => ['nullable', 'integer', 'min:0', 'gte:min_amount'],
            'is_individual' => ['boolean'],
            'can_escalate' => ['boolean'],
            'can_approve' => ['boolean'],
            'can_cancel' => ['boolean'],
            'can_reject' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['label' => 'nama jenjang', 'role' => 'peran pemutus', 'min_amount' => 'plafon minimum', 'max_amount' => 'plafon maksimum'];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ['max_amount.gte' => 'Plafon maksimum tidak boleh lebih kecil dari plafon minimum.'];
    }
}
