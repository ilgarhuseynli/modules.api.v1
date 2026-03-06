<?php

namespace Modules\Rental\Controllers;

use App\Classes\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Rental\Models\Badge;
use Modules\Rental\Requests\StoreBadgeRequest;
use Modules\Rental\Requests\UpdateBadgeRequest;
use Modules\Rental\Resources\BadgeMinlistResource;
use Modules\Rental\Resources\BadgeResource;
use Modules\Rental\Services\BadgeService;

class BadgeController extends Controller
{
    public function index(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, Badge::$sortable);

        $badges = Badge::with('translations')
            ->orderBy($sort['field'], $sort['direction'])
            ->paginate($limit);

        return BadgeResource::collection($badges);
    }

    public function minlist(Request $request): mixed
    {
        $limit = Helpers::manageLimitRequest($request->limit);
        $sort = Helpers::manageSortRequest($request->sort, $request->sort_type, Badge::$sortable);

        $badges = Badge::with('translations')
            ->where('is_active', true)
            ->orderBy($sort['field'], $sort['direction'])
            ->simplePaginate($limit);

        return BadgeMinlistResource::collection($badges);
    }

    public function store(StoreBadgeRequest $request, BadgeService $badgeService): mixed
    {
        $badge = $badgeService->create($request->validated());

        return response()->json(['id' => $badge->id]);
    }

    public function show(Badge $badge): mixed
    {
        $badge->load('translations');

        return response()->json(new BadgeResource($badge));
    }

    public function update(UpdateBadgeRequest $request, Badge $badge, BadgeService $badgeService): mixed
    {
        $badgeService->update($badge, $request->validated());

        return response()->json(['id' => $badge->id]);
    }

    public function destroy(Badge $badge): mixed
    {
        $badge->delete();

        return response()->noContent();
    }
}
