<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeParam = $this->route('category');
        $categoryId = is_object($routeParam) ? ($routeParam->id ?? 'NULL') : ($routeParam ?? 'NULL');

        return [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $categoryId,
            'description' => 'nullable|string',
            'status' => 'boolean',
            'order' => 'integer|min:0',
            'form_fields' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ];
    }
}


