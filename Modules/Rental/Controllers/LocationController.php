<?php

namespace Modules\Rental\Controllers;

use App\Classes\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Rental\Models\Location;
use Modules\Rental\Requests\StoreLocationRequest;
use Modules\Rental\Requests\UpdateLocationRequest;
use Modules\Rental\Resources\LocationMinlistResource;
use Modules\Rental\Resources\LocationResource;
use Modules\Rental\Services\LocationService;

class LocationController extends Controller
{
    public function index(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, Location::$sortable);

        $locations = Location::with('translations')
            ->orderBy($sort['field'], $sort['direction'])
            ->paginate($limit);

        return LocationResource::collection($locations);
    }

    public function minlist(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, Location::$sortable);

        $locations = Location::with('translations')
            ->where('is_active', true)
            ->orderBy($sort['field'], $sort['direction'])
            ->simplePaginate($limit);

        return LocationMinlistResource::collection($locations);
    }

    public function store(StoreLocationRequest $request, LocationService $locationService): mixed
    {
        $location = $locationService->create($request->validated());

        return response()->json(['id' => $location->id]);
    }

    public function show(Location $location): mixed
    {
        $location->load('translations');

        return response()->json(new LocationResource($location));
    }

    public function update(UpdateLocationRequest $request, Location $location, LocationService $locationService): mixed
    {
        $locationService->update($location, $request->validated());

        return response()->json(['id' => $location->id]);
    }

    public function destroy(Location $location): mixed
    {
        $location->delete();

        return response()->noContent();
    }
}
