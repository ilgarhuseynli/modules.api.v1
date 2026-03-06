<?php

namespace Modules\Rental\Services;

use Illuminate\Database\Eloquent\Model;

class SortOrderService
{
    public function getNextSortOrder(string $modelClass, array $scope = []): int
    {
        return (int) $modelClass::query()
            ->where($scope)
            ->max('sort_order') + 1;
    }

    public function insertAtPosition(string $modelClass, $requestedPosition, array $scope = []): int
    {
        $maxPosition = $this->getNextSortOrder($modelClass, $scope);
        $position = $this->clampPosition($requestedPosition, $maxPosition);

        $modelClass::query()
            ->where($scope)
            ->where('sort_order', '>=', $position)
            ->increment('sort_order');

        return $position;
    }

    public function moveToPosition(Model $model, $requestedPosition, array $scope = []): int
    {
        $oldPosition = (int) $model->sort_order;

        $model::query()
            ->where($scope)
            ->where('id', '<>', $model->id)
            ->where('sort_order', '>', $oldPosition)
            ->decrement('sort_order');

        $maxPosition = (int) $model::query()
            ->where($scope)
            ->where('id', '<>', $model->id)
            ->max('sort_order') + 1;

        $newPosition = $this->clampPosition($requestedPosition, $maxPosition);

        $model::query()
            ->where($scope)
            ->where('id', '<>', $model->id)
            ->where('sort_order', '>=', $newPosition)
            ->increment('sort_order');

        return $newPosition;
    }

    public function deleteAndShift(Model $model, array $scope = []): void
    {
        $sortOrder = (int) $model->sort_order;

        $model->delete();

        $model::query()
            ->where($scope)
            ->where('sort_order', '>', $sortOrder)
            ->decrement('sort_order');
    }

    public function massDestroy(string $modelClass, array $ids, array $scope = []): void
    {
        $modelClass::whereIn('id', $ids)->delete();

        $this->reindex($modelClass, $scope);
    }

    public function reindex(string $modelClass, array $scope = []): void
    {
        $ids = $modelClass::query()
            ->where($scope)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id');

        $order = 1;
        foreach ($ids as $id) {
            $modelClass::where('id', $id)->update(['sort_order' => $order]);
            $order++;
        }
    }

    private function clampPosition($requested, int $max): int
    {
        $max = max($max, 1);

        if (! is_numeric($requested) || (int) $requested < 1) {
            return $max;
        }

        return min((int) $requested, $max);
    }
}
