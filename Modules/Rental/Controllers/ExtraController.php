<?php

namespace Modules\Rental\Controllers;

use App\Classes\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Rental\Models\Extra;
use Modules\Rental\Requests\StoreExtraRequest;
use Modules\Rental\Requests\UpdateExtraRequest;
use Modules\Rental\Resources\ExtraMinlistResource;
use Modules\Rental\Resources\ExtraResource;
use Modules\Rental\Services\ExtraService;

class ExtraController extends Controller
{
    public function index(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, Extra::$sortable);

        $extras = Extra::with('translations')
            ->orderBy($sort['field'], $sort['direction'])
            ->paginate($limit);

        return ExtraResource::collection($extras);
    }

    public function minlist(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, Extra::$sortable);

        $extras = Extra::with('translations')
            ->where('is_active', true)
            ->orderBy($sort['field'], $sort['direction'])
            ->simplePaginate($limit);

        return ExtraMinlistResource::collection($extras);
    }

    public function store(StoreExtraRequest $request, ExtraService $extraService): mixed
    {
        $extra = $extraService->create($request->validated());

        return response()->json(['id' => $extra->id]);
    }

    public function show(Extra $extra): mixed
    {
        $extra->load('translations');

        return response()->json(new ExtraResource($extra));
    }

    public function update(UpdateExtraRequest $request, Extra $extra, ExtraService $extraService): mixed
    {
        $extraService->update($extra, $request->validated());

        return response()->json(['id' => $extra->id]);
    }

    public function destroy(Extra $extra): mixed
    {
        $extra->delete();

        return response()->noContent();
    }
}
