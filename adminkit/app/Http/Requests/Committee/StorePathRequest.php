<?php

namespace App\Http\Requests\Committee;

use App\Models\CommitteePath;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePathRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'condition' => filled($this->input('condition'))
                ? mb_strtoupper(trim((string) $this->input('condition')))
                : null,
            'product_id' => $this->input('product_id') ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'condition' => ['nullable', 'string', 'max:30'],
            'mechanism' => ['required', Rule::in(array_keys(CommitteePath::MECHANISMS))],
            'is_active' => ['boolean'],
            'note' => ['nullable', 'string', 'max:255'],
            'copy_from' => ['nullable', 'integer', Rule::exists('committee_paths', 'id')],
        ];
    }

    /**
     * Kombinasi produk + kondisi harus unik. Diperiksa di sini (bukan lewat
     * Rule::unique) karena kondisi kosong = Normal dan aturan `nullable`
     * membuat rule bawaan dilewati untuk nilai null.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $condition = $this->input('condition');

            $exists = CommitteePath::query()
                ->where('product_id', $this->input('product_id'))
                ->where(fn ($q) => $condition === null
                    ? $q->whereNull('condition')
                    : $q->where('condition', $condition))
                ->when($this->route('path')?->id, fn ($q, $id) => $q->whereKeyNot($id))
                ->exists();

            if ($exists) {
                $validator->errors()->add('condition', 'Jalur untuk produk dan kondisi tersebut sudah ada.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'produk',
            'condition' => 'kondisi/kategori',
            'mechanism' => 'mekanisme',
            'note' => 'catatan',
            'copy_from' => 'jalur sumber',
        ];
    }
}
