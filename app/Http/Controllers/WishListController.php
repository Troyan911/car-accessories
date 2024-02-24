<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    public function add(Product $product, Request $request)
    {
        $data = $this->validate($request);
        auth()->user()->addToWish($product, $data['type']);
        return redirect()->back();

    }

    public function remove(Product $product, Request $request)
    {
        $data = $this->validate($request);
        auth()->user()->removeFromWish($product, $data['type']);
        return redirect()->back();
    }
}
