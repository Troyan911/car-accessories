<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        $categories = Category::take(10)->get();
        $products = Product::with(['categories'])
            ->orderByDesc('id')
            ->take(80)
            ->get();

        return view('home', compact('products', 'categories'));
    }
}
