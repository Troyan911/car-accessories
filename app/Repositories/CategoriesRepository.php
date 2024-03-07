<?php

namespace App\Repositories;

use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Http\Requests\Categories\EditCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Str;

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
        try {
            $data = $request->validated();
            $data['slug'] = Str::of($data['name'])->slug()->value();

            $category->updateOrFail($data);

            return true;
        } catch (\Exception $exception) {
            logs()->warning($exception);

            return false;
        }
    }

    public function destroy(Category $category): bool
    {
        try {
            if ($category->childs()->exists()) {
                $category->childs()->update(['parent_id' => null]);
            }

            $category->deleteOrFail();

            return true;
        } catch (\Exception $exception) {
            logs()->warning($exception);

            return false;
        }
    }
}
