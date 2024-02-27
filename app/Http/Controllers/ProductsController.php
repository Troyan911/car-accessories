<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\Facades\Cart;

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
        $rowId = $this->getProductFromCart($product)?->rowId;
        $isInCart = (bool) $rowId;

        return view('products.show', compact('product', 'gallery', 'isInCart', 'rowId'));
    }

    protected function isProductInCart(Product $product): bool
    {
        return Cart::instance('cart')
            ->content()
            ->where('id', '=', $product->id)
            ->isNotEmpty();
    }

    protected function getProductFromCart(Product $product): ?CartItem
    {
        return Cart::instance('cart')
            ->content()
            ->where('id', '=', $product->id)
            ?->first();
    }
}
