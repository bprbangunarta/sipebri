<?php

namespace App\Http\Requests\Reference;

use App\Http\Controllers\ReferenceController;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi bersama seluruh modul referensi; aturannya diambil dari controller
 * yang menangani rute agar kolom cukup didefinisikan di satu tempat.
 */
class StoreReferenceRequest extends FormRequest
{
    private function controller(): ReferenceController
    {
        return $this->route()->getController();
    }

    protected function prepareForValidation(): void
    {
        $controller = $this->controller();
        $uppercase = $controller->uppercaseFields();
        $booleans = $controller->booleanFields();
        $numbers = $controller->numberFields();

        foreach (array_keys($controller->rules()) as $field) {
            if (in_array($field, $booleans, true)) {
                $this->merge([$field => (int) $this->boolean($field)]);

                continue;
            }

            if (in_array($field, $numbers, true)) {
                $this->merge([$field => (int) $this->input($field, 0)]);

                continue;
            }

            if (! $this->filled($field)) {
                continue;
            }

            $value = trim((string) $this->input($field));
            $this->merge([$field => in_array($field, $uppercase, true) ? mb_strtoupper($value) : $value]);
        }
    }

    public function rules(): array
    {
        return $this->controller()->rules($this->route('id') ? (int) $this->route('id') : null);
    }

    public function attributes(): array
    {
        return $this->controller()->attributeNames();
    }
}
