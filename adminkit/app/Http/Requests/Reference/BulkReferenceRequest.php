<?php

namespace App\Http\Requests\Reference;

use App\Http\Controllers\ReferenceController;
use Illuminate\Foundation\Http\FormRequest;

class BulkReferenceRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var ReferenceController $controller */
        $controller = $this->route()->getController();

        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', "exists:{$controller->table()},id"],
        ];
    }

    public function attributes(): array
    {
        return ['ids' => 'baris terpilih'];
    }
}
