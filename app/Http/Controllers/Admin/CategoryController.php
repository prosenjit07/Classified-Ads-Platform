<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * Create a new controller instance.
     *
     * @param CategoryService $categoryService
     * @return void
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $categories = $this->categoryService->getAllCategories();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Categories/Create', [
            'parentCategories' => $parentCategories,
            'fieldTypes' => $this->categoryService->getFieldTypes(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $this->categoryService->createCategory($request->validated());
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        $category = Category::with(['parent', 'children'])->findOrFail($id);
        
        return Inertia::render('Admin/Categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        try {
            $data = $this->categoryService->getCategoryForEdit($id);
            
            return Inertia::render('Admin/Categories/Edit', [
                'category' => $data['category'],
                'parentCategories' => $data['parentCategories'],
                'fieldTypes' => $this->categoryService->getFieldTypes(),
            ]);
        } catch (\Exception $e) {
            abort(404, 'Category not found');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCategoryRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $this->categoryService->updateCategory($id, $request->validated());
            
            return to_route('admin.categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $this->categoryService->deleteCategory($id);
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }
}
