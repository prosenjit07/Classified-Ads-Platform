<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brandId = $this->route('brand')?->id ?? 'NULL';

        return [
            'name' => 'required|string|max:255|unique:brands,name,' . $brandId,
            'slug' => 'required|string|max:255|unique:brands,slug,' . $brandId,
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|max:2048',
        ];
    }
}


