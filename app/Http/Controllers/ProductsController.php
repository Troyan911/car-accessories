<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::available()->paginate(12);

        return view('product.index', compact('products'));

    }

    public function show(Product $product)
    {
        //        $product->load(['images', 'categories']);
        $gallery = collect($product->images()->get()->map(fn ($image) => $image->url));
        $gallery->prepend($product->thumbnailUrl);

        return view('products.show', compact('product', 'gallery'));
    }
}
