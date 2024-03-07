<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('products', \App\Http\Controllers\Api\V1\ProductsController::class);
Route::apiResource('categories', \App\Http\Controllers\Api\V1\CategoriesController::class);
