<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\Facades\Cart;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::with(['categories'])
            ->orderByDesc('id')
            ->paginate(24);
        return view('products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $gallery = collect($product->images()->get()->map(fn($image) => $image->url));
        $gallery->prepend($product->thumbnailUrl);
        $rowId = $this->getProductsFromCart($product)?->rowId;
        $isInCart = (bool)$rowId;

        return view('products.show', compact('product', 'gallery', 'isInCart', 'rowId'));
    }

    protected function getProductsFromCart(Product $product): ?CartItem
    {
        return Cart::instance('cart')
            ->content()
            ->where('id', '=', $product->id)
            ?->first();
    }
}
