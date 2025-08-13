<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WishlistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /**
     * Determine if the user is authorized to make this request.
     */
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated users can add/update wishlist items
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            // Common rules for create/update
            'notes' => ['nullable', 'string', 'max:1000'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:5'],
        ];

        // Additional rules for specific HTTP methods
        if ($this->isMethod('post')) {
            $rules['product_id'] = [
                'required',
                'integer',
                'exists:products,id',
                // Prevent duplicate wishlist items
                function ($attribute, $value, $fail) {
                    if ($this->user() && \App\Models\Wishlist::where('user_id', $this->user()->id)
                        ->where('product_id', $value)
                        ->exists()) {
                        $fail('This product is already in your wishlist.');
                    }
                },
            ];
        }

        return $rules;
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product to add to your wishlist.',
            'product_id.exists' => 'The selected product does not exist.',
            'notes.max' => 'Notes cannot be longer than 1000 characters.',
            'priority.integer' => 'Priority must be a number.',
            'priority.min' => 'Priority must be at least 0.',
            'priority.max' => 'Priority cannot be greater than 5.',
        ];
    }
}
