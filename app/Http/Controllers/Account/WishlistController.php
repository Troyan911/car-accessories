<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

class WishlistController extends Controller
{
    public function __invoke()
    {
        $products = auth()->user()->wishes()->paginate(5);

        return view('account.wishlist', compact('products'));
    }
}
