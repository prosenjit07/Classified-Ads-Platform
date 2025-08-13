<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display the admin dashboard with statistics.
     *
     * @return \Inertia\Response
     */
    public function dashboard()
    {
        try {
            $stats = [
                'total_products' => Product::count(),
                'total_categories' => Category::count(),
                'total_brands' => Brand::count(),
                'latest_products' => Product::with(['category', 'brand'])
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->price,
                            'status' => $product->is_active ? 'Active' : 'Inactive',
                            'category' => $product->category->name ?? 'N/A',
                            'brand' => $product->brand->name ?? 'N/A',
                        ];
                    })
                    ->toArray(),
            ];

            return Inertia::render('Admin/Dashboard', [
                'stats' => $stats,
                'flash' => [
                    'success' => session('success'),
                    'error' => session('error')
                ]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Display the admin settings page.
     *
     * @return \Inertia\Response
     */
    public function settings()
    {
        return Inertia::render('Admin/Settings');
    }

    /**
     * Display the admin profile page.
     *
     * @return \Inertia\Response
     */
    public function profile()
    {
        return Inertia::render('Admin/Profile', [
            'user' => auth()->user()
        ]);
    }
}