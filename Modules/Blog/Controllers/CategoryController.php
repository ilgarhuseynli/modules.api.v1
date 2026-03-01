<?php

namespace Modules\Blog\Controllers;

use App\Classes\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Blog\Models\Category;
use Modules\Blog\Requests\StoreCategoryRequest;
use Modules\Blog\Requests\UpdateCategoryRequest;
use Modules\Blog\Resources\CategoryResource;
use Modules\Blog\Services\CategoryService;

class CategoryController extends Controller
{
    public function index(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, Category::$sortable);

        $categories = Category::with('translations', 'children.translations')
            ->orderBy($sort['field'], $sort['direction'])
            ->paginate($limit);

        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request, CategoryService $categoryService): mixed
    {
        $category = $categoryService->create($request->validated());

        return response()->json(['id' => $category->id]);
    }

    public function show(Category $category): mixed
    {
        $category->load('translations', 'children.translations', 'parent.translations');

        return response()->json(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, Category $category, CategoryService $categoryService): mixed
    {
        $categoryService->update($category, $request->validated());

        return response()->json(['id' => $category->id]);
    }

    public function destroy(Category $category): mixed
    {
        $category->delete();

        return response()->noContent();
    }
}
