<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProductRequest;
use App\Http\Requests\Products\EditProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductsRepositoryContract;
use Illuminate\Support\Str;

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
            ->paginate(5);

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
        return $repository->create($request)
            ? redirect()->route('admin.products.index')
            : redirect()->back()->withInput();

        //notify()->success("Product '$data[title]' was created!")
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $productCatIds = $product->categories()->get()->pluck('id')->toArray();


        return view('admin.products.edit', compact(['product', 'categories', 'productCatIds']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['slug'] = Str::of($data['title'])->slug()->value();

        $product->updateOrFail($data);
        notify()->success("Product '$data[title]' was updated!");

        return redirect()->route('admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->middleware('permission:'.config('permission.permissions.delete'));
        $title = $product->title;


//        if ($product->pivot()->exists()) {
//            $product->pivot()->where('product_id', '=', $product->id)->delete();
//        }

        $product->deleteOrFail();
        notify()->success("Product '$title' was deleted!");

        return redirect()->route('admin.products.index');
    }
}
