<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryField extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'type',
        'options',
        'is_required',
        'order',
        'validation_rules',
        'default_value',
        'help_text',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
        'validation_rules' => 'array',
        'order' => 'integer',
    ];

    /**
     * Get the category that owns the field.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the options as an array.
     *
     * @return array
     */
    public function getOptionsArrayAttribute(): array
    {
        if (empty($this->options)) {
            return [];
        }

        return array_map('trim', explode(',', $this->options));
    }

    /**
     * Get the validation rules for this field.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        $rules = [];

        // Add required rule if needed
        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Add type-specific validation rules
        switch ($this->type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'url':
                $rules[] = 'url';
                break;
            case 'date':
            case 'datetime-local':
                $rules[] = 'date';
                break;
            case 'select':
            case 'checkbox':
            case 'radio':
                if (!empty($this->options_array)) {
                    $rules[] = 'in:' . implode(',', $this->options_array);
                }
                break;
        }

        // Add any additional validation rules
        if (!empty($this->validation_rules)) {
            $rules = array_merge($rules, $this->validation_rules);
        }

        return $rules;
    }
}
