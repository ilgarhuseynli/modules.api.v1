<?php

namespace Modules\Blog\Controllers;

use App\Classes\Helpers;
use App\Http\Controllers\Controller;
use App\Services\FileService;
use Illuminate\Http\Request;
use Modules\Blog\Models\Category;
use Modules\Blog\Requests\StoreCategoryRequest;
use Modules\Blog\Requests\UpdateCategoryRequest;
use Modules\Blog\Resources\CategoryMinlistResource;
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

    public function minlist(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, Category::$sortable);

        $categories = Category::with('translations')
            ->where('is_active', true)
            ->orderBy($sort['field'], $sort['direction'])
            ->simplePaginate($limit);

        return CategoryMinlistResource::collection($categories);
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

    public function fileupload(Request $request, Category $category, FileService $fileService): mixed
    {
        $request->validate([
            'type' => 'required|in:image',
            'file_id' => 'required|numeric',
        ]);

        $fileData = $fileService->storeTmpFile($category, $request->input('file_id'), $request->type);

        if (! $fileData) {
            return response()->json(['message' => 'File not saved. Please try again.'], 402);
        }

        $category->update(['image_id' => $fileData['id']]);

        return response()->json($fileData);
    }

    public function filedelete(Request $request, Category $category, FileService $fileService): mixed
    {
        $request->validate([
            'file_id' => 'required|exists:files,id',
        ]);

        $fileService->deleteFile($category->image);
        $category->update(['image_id' => null]);

        return response()->noContent();
    }
}
