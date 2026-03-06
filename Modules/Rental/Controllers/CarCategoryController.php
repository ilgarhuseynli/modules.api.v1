<?php

namespace Modules\Rental\Controllers;

use App\Classes\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Rental\Models\CarCategory;
use Modules\Rental\Requests\StoreCarCategoryRequest;
use Modules\Rental\Requests\UpdateCarCategoryRequest;
use Modules\Rental\Resources\CarCategoryMinlistResource;
use Modules\Rental\Resources\CarCategoryResource;
use Modules\Rental\Services\CarCategoryService;

class CarCategoryController extends Controller
{
    public function index(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, CarCategory::$sortable);

        $categories = CarCategory::with('translations')
            ->withCount('cars')
            ->orderBy($sort['field'], $sort['direction'])
            ->paginate($limit);

        return CarCategoryResource::collection($categories);
    }

    public function minlist(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, CarCategory::$sortable);

        $categories = CarCategory::with('translations')
            ->where('is_active', true)
            ->orderBy($sort['field'], $sort['direction'])
            ->simplePaginate($limit);

        return CarCategoryMinlistResource::collection($categories);
    }

    public function store(StoreCarCategoryRequest $request, CarCategoryService $categoryService): mixed
    {
        $category = $categoryService->create($request->validated());

        return response()->json(['id' => $category->id]);
    }

    public function show(CarCategory $carCategory): mixed
    {
        $carCategory->load('translations');

        return response()->json(new CarCategoryResource($carCategory));
    }

    public function update(UpdateCarCategoryRequest $request, CarCategory $carCategory, CarCategoryService $categoryService): mixed
    {
        $categoryService->update($carCategory, $request->validated());

        return response()->json(['id' => $carCategory->id]);
    }

    public function destroy(CarCategory $carCategory): mixed
    {
        $carCategory->delete();

        return response()->noContent();
    }
}
