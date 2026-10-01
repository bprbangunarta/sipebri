<?php

namespace App\Http\Requests;

use App\Models\CommitteePath;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommitteePathRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'condition' => filled($this->input('condition')) ? mb_strtoupper(trim((string) $this->input('condition'))) : null,
            'product_id' => $this->input('product_id') ?: null,
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'condition' => ['nullable', 'string', 'max:30'],
            'mechanism' => ['required', Rule::in(array_keys(CommitteePath::MECHANISMS))],
            'is_active' => ['boolean'],
            'note' => ['nullable', 'string', 'max:255'],
            'copy_from' => ['nullable', 'integer', Rule::exists('committee_paths', 'id')],
            'follows_default' => ['boolean'],
        ];
    }

    /**
     * Product + condition must be unique. Checked here rather than with Rule::unique because an
     * empty condition means "Normal" and nullable values skip the built-in rule.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $condition = $this->input('condition');
            $current = $this->route('path');
            $ignoreId = $current instanceof CommitteePath ? $current->id : null;

            $exists = CommitteePath::query()
                ->where('product_id', $this->input('product_id'))
                ->where(fn ($q) => $condition === null ? $q->whereNull('condition') : $q->where('condition', $condition))
                ->when($ignoreId, fn ($q, int $id) => $q->whereKeyNot($id))
                ->exists();

            if ($exists) {
                $validator->errors()->add('condition', 'Jalur untuk produk dan kondisi ini sudah ada.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['product_id' => 'produk', 'condition' => 'kondisi/kategori', 'copy_from' => 'jalur sumber'];
    }
}
