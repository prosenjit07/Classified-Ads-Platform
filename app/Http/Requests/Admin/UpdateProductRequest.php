<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeProduct = $this->route('product');
        $productId = 'NULL';
        if (is_object($routeProduct) && isset($routeProduct->id)) {
            $productId = $routeProduct->id;
        } elseif (is_numeric($routeProduct)) {
            $productId = (int) $routeProduct;
        } elseif (is_string($routeProduct)) {
            $found = Product::where('slug', $routeProduct)->first();
            if ($found) {
                $productId = $found->id;
            }
        }
        $categoryId = $this->input('category_id');
        $category = $categoryId ? Category::find($categoryId) : null;

        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $productId,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $productId,
            'quantity' => 'nullable|integer|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'manage_stock' => 'nullable|boolean',
            'stock_status' => 'nullable|in:in_stock,out_of_stock,on_backorder',
            'condition' => 'required|in:new,used,refurbished',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive,draft',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'attributes' => 'nullable|array',
        ];

        if ($category && method_exists($category, 'getFieldValidationRules')) {
            $rules = array_merge($rules, $category->getFieldValidationRules());
        }

        return $rules;
    }
}


