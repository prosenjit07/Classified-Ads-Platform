<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->input('category_id');
        $category = $categoryId ? Category::find($categoryId) : null;

        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'quantity' => 'required|integer|min:0',
            'in_stock' => 'boolean',
            'condition' => 'required|in:new,used,refurbished',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'required|in:active,inactive,draft',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'fields' => 'nullable|array',
        ];

        if ($category && method_exists($category, 'getFieldValidationRules')) {
            $rules = array_merge($rules, $category->getFieldValidationRules());
        }

        return $rules;
    }
}


