<?php

namespace App\Repositories;

use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Http\Requests\Categories\EditCategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ImageRepositoryContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\True_;

class CategoriesRepository implements Contracts\CategoriesRepositoryContract
{
    public function __construct()
    {
    }

    public function create(CreateCategoryRequest $request): bool
    {
        try {
            $data = $request->validated();
            $data['slug'] = Str::of($data['name'])->slug()->value();

            Category::create($data);
            return true;
        } catch (\Exception $exception) {
            logs()->warning($exception);
            return false;
        }
    }

    public function update(Category $category, EditCategoryRequest $request): bool
    {
        $data = $request->validated();
        $data['slug'] = Str::of($data['name'])->slug()->value();

        return $category->updateOrFail($data);
    }

    public function destroy(Category $category): bool
    {
        if ($category->childs()->exists()) {
            $category->childs()->update(['parent_id' => null]);
        }

        return $category->deleteOrFail();
    }
}
