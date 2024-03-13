<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProductRequest;
use App\Http\Requests\Products\EditProductRequest;
use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\Products\ProductsCollection;
use App\Models\Product;
use App\Repositories\Contracts\ProductsRepositoryContract;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //        $this->authorize('viewAny', Product::class);
        $products = Product::with(['categories', 'images'])
            ->orderByDesc('id')
            ->paginate(5);

        return (new ProductsCollection($products))
            ->additional(
                [
                    'meta_data' => [
                        'total' => $products->total(),
                        'per_page' => $products->perPage(),
                        'page' => $products->currentPage(),
                        'to' => $products->lastPage(),
                        'path' => $products->path(),
                        'next' => $products->nextPageUrl(),
                    ],
                ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request, ProductsRepositoryContract $repository)
    {
        return new ProductResource($repository->create($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditProductRequest $request, Product $product, ProductsRepositoryContract $repository)
    {
        $repository->update($product, $request);

        return new ProductResource(Product::find($product->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductsRepositoryContract $repository)
    {
        return $repository->destroy($product);
    }
}
