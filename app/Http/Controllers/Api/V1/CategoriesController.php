<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Http\Requests\Categories\EditCategoryRequest;
use App\Http\Resources\Categories\CategoriesCollection;
use App\Http\Resources\Categories\CategoryResource;
use App\Models\Category;
use App\Repositories\Contracts\CategoriesRepositoryContract;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Category::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with(['parent'])
            ->paginate(10);

        return (new CategoriesCollection($categories))->additional(
            [
                'meta_data' => [
                    'total' => $categories->total(),
                    'per_page' => $categories->perPage(),
                    'page' => $categories->currentPage(),
                    'to' => $categories->lastPage(),
                    'path' => $categories->path(),
                    'next' => $categories->nextPageUrl(),
                ],
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequest $request, CategoriesRepositoryContract $repository)
    {
        return new CategoryResource($repository->create($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
//        $this->authorize('view', Category::find($id));
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditCategoryRequest $request, Category $category, CategoriesRepositoryContract $repository)
    {
        $repository->update($category, $request);

        return new CategoryResource(Category::find($category->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category, CategoriesRepositoryContract $repository)
    {
        return $repository->destroy($category);
    }
}
