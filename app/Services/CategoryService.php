<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * Get all categories with parent relationship
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCategories()
    {
        return Category::with('parent')
            ->orderBy('parent_id')
            ->orderBy('order')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'parent' => $category->parent ? $category->parent->name : null,
                    'status' => $category->status,
                    'order' => $category->order,
                    'created_at' => $category->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    /**
     * Get a category by ID with parent categories for selection
     *
     * @param int $id
     * @return array
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getCategoryForEdit($id)
    {
        $category = Category::findOrFail($id);
        
        $parentCategories = Category::where('id', '!=', $category->id)
            ->where(function($query) use ($category) {
                $query->whereNull('parent_id')
                      ->orWhere('parent_id', '!=', $category->id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return [
            'category' => $category,
            'parentCategories' => $parentCategories
        ];
    }

    /**
     * Create a new category
     *
     * @param array $data
     * @return \App\Models\Category
     */
    public function createCategory(array $data)
    {
        DB::beginTransaction();
        
        try {
            $data['slug'] = $this->generateSlug($data['name']);
            
            if (isset($data['form_fields'])) {
                $data['form_fields'] = $this->processFormFields($data['form_fields']);
            }
            
            $category = Category::create($data);
            
            DB::commit();
            
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating category: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing category
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Category
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateCategory($id, array $data)
    {
        $category = Category::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            if (isset($data['name'])) {
                $data['slug'] = $this->generateSlug($data['name'], $id);
            }
            
            if (isset($data['form_fields'])) {
                $data['form_fields'] = $this->processFormFields($data['form_fields']);
            }
            
            $category->update($data);
            
            DB::commit();
            
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating category: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a category
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has children
        if ($category->children()->exists()) {
            throw new \Exception('Cannot delete category with subcategories');
        }
        
        return $category->delete();
    }

    /**
     * Generate a URL-friendly slug from the given string
     *
     * @param string $title
     * @param int|null $id
     * @return string
     */
    protected function generateSlug($title, $id = null)
    {
        $slug = Str::slug($title);
        
        // Check if slug already exists
        $query = Category::where('slug', $slug);
        
        if ($id) {
            $query->where('id', '!=', $id);
        }
        
        if ($query->exists()) {
            $slug = $slug . '-' . time();
        }
        
        return $slug;
    }

    /**
     * Process form fields before saving
     *
     * @param array $fields
     * @return array
     */
    protected function processFormFields($fields)
    {
        if (!is_array($fields)) {
            return [];
        }
        
        $processed = [];
        
        foreach ($fields as $field) {
            if (empty($field['name'])) {
                continue;
            }
            
            $processed[] = [
                'name' => $field['name'],
                'label' => $field['label'] ?? $field['name'],
                'type' => $field['type'] ?? 'text',
                'required' => $field['required'] ?? false,
                'options' => $field['options'] ?? [],
            ];
        }
        
        return $processed;
    }

    /**
     * Get available field types for the category form
     *
     * @return array
     */
    public function getFieldTypes()
    {
        return [
            ['value' => 'text', 'label' => 'Text'],
            ['value' => 'textarea', 'label' => 'Text Area'],
            ['value' => 'number', 'label' => 'Number'],
            ['value' => 'email', 'label' => 'Email'],
            ['value' => 'select', 'label' => 'Dropdown'],
            ['value' => 'checkbox', 'label' => 'Checkbox'],
            ['value' => 'radio', 'label' => 'Radio Button'],
            ['value' => 'time', 'label' => 'Time'],
            ['value' => 'datetime', 'label' => 'Date & Time'],
            ['value' => 'file', 'label' => 'File Upload'],
            ['value' => 'image', 'label' => 'Image Upload'],
        ];
    }
}
