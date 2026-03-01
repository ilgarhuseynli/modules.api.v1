<?php

namespace Modules\Blog\Services;

use Illuminate\Database\Eloquent\Model;

class SortOrderService
{
    /**
     * Get next sort_order for a model (append to end).
     */
    public function getNextSortOrder(string $modelClass, array $scope = []): int
    {
        return (int) $modelClass::query()
            ->where($scope)
            ->max('sort_order') + 1;
    }

    /**
     * Insert a new record at the given sort position.
     * Shifts existing records down to make room.
     */
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

    /**
     * Move a record to a new position.
     * 1) Remove from old position (others slide up)
     * 2) Insert at new position (others slide down)
     */
    public function moveToPosition(Model $model, $requestedPosition, array $scope = []): int
    {
        $oldPosition = (int) $model->sort_order;

        // Step 1: Close the gap — slide up all items after old position
        $model::query()
            ->where($scope)
            ->where('id', '<>', $model->id)
            ->where('sort_order', '>', $oldPosition)
            ->decrement('sort_order');

        // Step 2: Calculate max position (excluding current model)
        $maxPosition = (int) $model::query()
            ->where($scope)
            ->where('id', '<>', $model->id)
            ->max('sort_order') + 1;

        $newPosition = $this->clampPosition($requestedPosition, $maxPosition);

        // Step 3: Make room — slide down items at/after new position
        $model::query()
            ->where($scope)
            ->where('id', '<>', $model->id)
            ->where('sort_order', '>=', $newPosition)
            ->increment('sort_order');

        return $newPosition;
    }

    /**
     * Delete record and slide up remaining records.
     */
    public function deleteAndShift(Model $model, array $scope = []): void
    {
        $sortOrder = (int) $model->sort_order;

        $model->delete();

        $model::query()
            ->where($scope)
            ->where('sort_order', '>', $sortOrder)
            ->decrement('sort_order');
    }

    /**
     * Mass delete records and reindex sort_order.
     */
    public function massDestroy(string $modelClass, array $ids, array $scope = []): void
    {
        $modelClass::whereIn('id', $ids)->delete();

        $this->reindex($modelClass, $scope);
    }

    /**
     * Reindex sort_order for records (1, 2, 3, ...).
     */
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

    /**
     * Clamp requested position between 1 and max.
     */
    private function clampPosition($requested, int $max): int
    {
        $max = max($max, 1);

        if (! is_numeric($requested) || (int) $requested < 1) {
            return $max;
        }

        return min((int) $requested, $max);
    }
}
