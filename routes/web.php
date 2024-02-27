<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', App\Http\Controllers\HomeController::class)->name('home');

Route::resource('products', \App\Http\Controllers\ProductsController::class)->only(['index', 'show']);
Route::resource('categories', \App\Http\Controllers\CategoriesController::class)->only(['index', 'show']);

Auth::routes();

Route::get('test', function () {
    app(\App\Services\Contract\FileStorageServiceContract::class)->remove('test');
});

Route::name('ajax.')->prefix('ajax')->middleware('auth')->group(function () {
    Route::group(['role:admin|moderator'], function () {
        Route::post('products/{product}/images', [\App\Http\Controllers\Ajax\Products\ImagesController::class, 'store'])->name('products.images.store');
        Route::delete('images/{image}', \App\Http\Controllers\Ajax\RemoveImagesController::class)->name('images.destroy');
    });
});

Route::name('admin.')
    ->prefix('admin')
    ->middleware(['role:admin|moderator'])
    ->group(
        function () {
            //admin.dashboard
            Route::get('dashboard', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');
            Route::resource('categories', \App\Http\Controllers\Admin\CategoriesController::class)
                ->except(['show']);
            Route::resource('products', \App\Http\Controllers\Admin\ProductsController::class)
                ->except(['show']);
        }
    );
