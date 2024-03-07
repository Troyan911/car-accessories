<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProductRequest;
use App\Http\Requests\Products\EditProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductsRepositoryContract;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['categories'])
            ->withCount('categories')
            ->sortable()
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request, ProductsRepositoryContract $repository)
    {
        if ($item = $repository->create($request)) {
            notify()->success("Product $item->name was created!");

            return redirect()->route('admin.products.index');
        } else {
            notify()->warning("Product wasn't created!");

            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $productCategoriesId = $product->categories()->get()->pluck('id')->toArray();

        return view('admin.products.edit', compact(['product', 'categories', 'productCategoriesId']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditProductRequest $request, Product $product, ProductsRepositoryContract $repository)
    {
        if ($repository->update($product, $request)) {
            notify()->success("Product '$product->title' was updated!");

            return redirect()->route('admin.products.edit', $product);
        } else {
            notify()->warning("Product '$product->title' wasn't updated!");

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductsRepositoryContract $repository)
    {
        $title = $product->title;
        $this->middleware('permission:'.config('permission.permissions.delete'));
        $repository->destroy($product)
            ? notify()->success("Product '$title' was deleted!")
            : notify()->warning("Product '$title' wasn't deleted!");

        return redirect()->route('admin.products.index');
    }
}
