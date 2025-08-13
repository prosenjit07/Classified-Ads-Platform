<?php

namespace App\Http\Controllers\Admin;

 use App\Http\Controllers\Controller;
 use App\Http\Requests\Admin\StoreCategoryRequest;
 use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('parent')
            ->orderBy('parent_id')
            ->orderBy('order')
            ->get()
            ->toTree();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Categories/Create', [
            'parentCategories' => $parentCategories,
            'fieldTypes' => $this->getFieldTypes(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

        // Generate slug from name if not provided
        $validated['slug'] = Str::slug($request->input('slug') ?: $request->name);

        // Handle form fields
        if (isset($validated['form_fields'])) {
            $validated['form_fields'] = $this->processFormFields($validated['form_fields']);
        }

        try {
            DB::beginTransaction();
            
            $category = Category::create($validated);
            
            DB::commit();
            
            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return Inertia::render('Admin/Categories/Show', [
            'category' => $category->load('parent', 'children')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::where('id', '!=', $category->id)
            ->where(function($query) use ($category) {
                $query->whereNull('parent_id')
                      ->orWhere('parent_id', '!=', $category->id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category,
            'parentCategories' => $parentCategories,
            'fieldTypes' => $this->getFieldTypes(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

        // Generate slug from name if not provided
        $validated['slug'] = Str::slug($request->input('slug') ?: $request->name);

        // Handle form fields
        if (isset($validated['form_fields'])) {
            $validated['form_fields'] = $this->processFormFields($validated['form_fields']);
        }

        try {
            DB::beginTransaction();
            
            $category->update($validated);
            
            DB::commit();
            
            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Prevent deletion if category has children
        if ($category->children()->exists()) {
            return back()->with('error', 'Cannot delete category with subcategories.');
        }

        try {
            DB::beginTransaction();
            
            $category->delete();
            
            DB::commit();
            
            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }

    /**
     * Get the available field types for dynamic form fields.
     *
     * @return array
     */
    protected function getFieldTypes(): array
    {
        return [
            ['value' => 'text', 'label' => 'Text'],
            ['value' => 'number', 'label' => 'Number'],
            ['value' => 'email', 'label' => 'Email'],
            ['value' => 'url', 'label' => 'URL'],
            ['value' => 'tel', 'label' => 'Phone'],
            ['value' => 'date', 'label' => 'Date'],
            ['value' => 'datetime-local', 'label' => 'Date & Time'],
            ['value' => 'select', 'label' => 'Dropdown'],
            ['value' => 'checkbox', 'label' => 'Checkbox'],
            ['value' => 'radio', 'label' => 'Radio Buttons'],
            ['value' => 'textarea', 'label' => 'Text Area'],
        ];
    }

    /**
     * Process and validate form fields before saving.
     *
     * @param array $fields
     * @return array
     */
    protected function processFormFields(array $fields): array
    {
        $processed = [];
        
        foreach ($fields as $field) {
            if (empty($field['label']) || empty($field['name'])) {
                continue;
            }
            
            $processed[] = [
                'label' => $field['label'],
                'name' => $field['name'],
                'type' => $field['type'] ?? 'text',
                'required' => $field['required'] ?? false,
                'options' => $field['options'] ?? [],
                'placeholder' => $field['placeholder'] ?? '',
                'validation' => $field['validation'] ?? null,
            ];
        }
        
        return $processed;
    }
}
