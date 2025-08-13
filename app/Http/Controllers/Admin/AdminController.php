<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Inertia\Response
     */
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_products' => 0, // Will be replaced with actual counts
                'total_brands' => 0,
                'total_categories' => 0,
                'total_users' => 0,
            ]
        ]);
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