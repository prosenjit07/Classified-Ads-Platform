<?php

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\BrandController;
use Illuminate\Support\Facades\Route;

// Public API endpoints
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/brands', [BrandController::class, 'index']);