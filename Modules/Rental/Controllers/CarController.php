<?php

namespace Modules\Rental\Controllers;

use App\Classes\Helpers;
use App\Http\Controllers\Controller;
use App\Services\FileService;
use Illuminate\Http\Request;
use Modules\Rental\Models\Car;
use Modules\Rental\Requests\StoreCarRequest;
use Modules\Rental\Requests\UpdateCarRequest;
use Modules\Rental\Resources\CarResource;
use Modules\Rental\Services\CarService;

class CarController extends Controller
{
    public function index(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, Car::$sortable);

        $cars = Car::with('translations', 'category.translations', 'badges.translations', 'avatar', 'images')
            ->filter($request->only(['brand', 'model', 'category_id', 'transmission', 'fuel_type', 'body_type', 'is_active']))
            ->orderBy($sort['field'], $sort['direction'])
            ->paginate($limit);

        return CarResource::collection($cars);
    }

    public function store(StoreCarRequest $request, CarService $carService): mixed
    {
        $car = $carService->create($request->validated());

        return response()->json(['id' => $car->id]);
    }

    public function show(Car $car): mixed
    {
        $car->load('translations', 'category.translations', 'badges.translations', 'extras.translations', 'avatar', 'images');

        return response()->json(new CarResource($car));
    }

    public function update(UpdateCarRequest $request, Car $car, CarService $carService): mixed
    {
        $carService->update($car, $request->validated());

        return response()->json(['id' => $car->id]);
    }

    public function destroy(Car $car): mixed
    {
        $car->delete();

        return response()->noContent();
    }

    public function fileupload(Request $request, Car $car, FileService $fileService): mixed
    {
        $request->validate([
            'type' => 'required|in:avatar,images',
            'file_id' => 'required|numeric',
        ]);

        $fileData = $fileService->storeTmpFile($car, $request->input('file_id'), $request->type);

        if (! $fileData) {
            return response()->json(['message' => 'File not saved. Please try again.'], 402);
        }

        if ($request->type === 'avatar') {
            $car->update(['avatar_id' => $fileData['id']]);
        }

        return response()->json($fileData);
    }

    public function filedelete(Request $request, Car $car, FileService $fileService): mixed
    {
        $request->validate([
            'file_id' => 'required|exists:files,id',
        ]);

        if ($car->avatar_id == $request->file_id) {
            $fileService->deleteFile($car->avatar);
            $car->update(['avatar_id' => null]);
        } else {
            $fileData = $car->images()->where('id', $request->file_id)->first();
            $fileService->deleteFile($fileData);
        }

        return response()->noContent();
    }
}
