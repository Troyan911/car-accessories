<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Http\Requests\Categories\EditCategoryRequest;
use App\Models\Category;

interface CategoriesRepositoryContract
{
    public function create(CreateCategoryRequest $request): bool;

    public function update(Category $category, EditCategoryRequest $request): bool;
    public function destroy(Category $category): bool;
}
