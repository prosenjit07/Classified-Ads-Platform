<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    /**
     * Get all active brands
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $brands = Brand::active()
            ->select(['id', 'name', 'slug', 'description'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands
        ]);
    }
}