<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Http\Requests\Categories\EditCategoryRequest;
use App\Models\Category;
use App\Repositories\Contracts\CategoriesRepositoryContract;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with(['products', 'parent'])
            ->withCount('products')
            ->sortable()
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequest $request, CategoriesRepositoryContract $repository)
    {
        if ($repository->create($request)) {
            notify()->success('Category was created!');

            return redirect()->route('admin.categories.index');
        } else {
            notify()->warning("Category wasn't created!");

            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)->get();

        return view('admin.categories.edit', compact(['category', 'categories']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditCategoryRequest $request, Category $category, CategoriesRepositoryContract $repository)
    {
        if ($repository->update($category, $request)) {
            notify()->success("Category '$category->name' was updated!");

            return redirect()->route('admin.categories.index');
        } else {
            notify()->warning("Category '$category->name' wasn't updated!");

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category, CategoriesRepositoryContract $repository)
    {
        $name = $category->name;
        $this->middleware('permission:'.config('permission.permissions.delete'));
        $repository->destroy($category)
            ? notify()->success("Category '$name' was deleted!")
            : notify()->warning("Category '$name' wasn't deleted!");

        return redirect()->route('admin.categories.index');
    }
}
