<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        $products = $category->products;
        $categories = $category->childs;

        return view('categories.show', compact('category', 'products', 'categories'));
    }
}
