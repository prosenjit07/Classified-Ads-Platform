<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'status',
        'form_fields',
        'order',
        'description',
        'meta_title',
        'meta_description',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'form_fields' => 'array',
    ];

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    /**
     * Get the fields for the category.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(CategoryField::class)->orderBy('order');
    }
    
    /**
     * Get all ancestor categories including parent, grandparent, etc.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAncestors()
    {
        $ancestors = collect();
        $current = $this;
        
        while ($current->parent) {
            $ancestors->push($current->parent);
            $current = $current->parent;
        }
        
        return $ancestors->reverse();
    }
    
    /**
     * Get all fields including those from parent categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFields()
    {
        $fields = $this->fields;
        
        // Add fields from parent categories
        $ancestors = $this->getAncestors();
        foreach ($ancestors as $ancestor) {
            $fields = $fields->merge($ancestor->fields);
        }
        
        return $fields->unique('name');
    }
    
    /**
     * Get the validation rules for all fields in this category.
     *
     * @return array
     */
    public function getFieldValidationRules(): array
    {
        $rules = [];
        
        foreach ($this->getAllFields() as $field) {
            $rules["fields.{$field->name}"] = $field->getValidationRules();
        }
        
        return $rules;
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get all descendants of the category.
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}