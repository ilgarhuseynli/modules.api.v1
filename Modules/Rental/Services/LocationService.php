<?php

namespace Modules\Rental\Services;

use Illuminate\Support\Facades\DB;
use Modules\Rental\Models\Location;

class LocationService
{
    public function __construct(
        protected SortOrderService $sortOrderService,
    ) {}

    public function create(array $data): Location
    {
        return DB::transaction(function () use ($data) {
            $sortOrder = $this->sortOrderService->insertAtPosition(
                Location::class,
                $data['sort_order'] ?? null,
            );

            $location = Location::create([
                'is_active' => $data['is_active'] ?? true,
                'price' => $data['price'] ?? null,
                'sort_order' => $sortOrder,
            ]);

            foreach ($data['translations'] as $locale => $translation) {
                if (empty($translation['name'])) {
                    continue;
                }

                $location->translations()->create([
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'address' => $translation['address'] ?? null,
                ]);
            }

            return $location->load('translations');
        });
    }

    public function update(Location $location, array $data): Location
    {
        return DB::transaction(function () use ($location, $data) {
            $sortOrder = $location->sort_order;

            if (isset($data['sort_order']) && (int) $data['sort_order'] !== $location->sort_order) {
                $sortOrder = $this->sortOrderService->moveToPosition(
                    $location,
                    $data['sort_order'],
                );
            }

            $location->update([
                'is_active' => $data['is_active'] ?? $location->is_active,
                'price' => array_key_exists('price', $data) ? $data['price'] : $location->price,
                'sort_order' => $sortOrder,
            ]);

            if (isset($data['translations'])) {
                foreach ($data['translations'] as $locale => $translation) {
                    if (empty($translation['name'])) {
                        continue;
                    }

                    $location->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $translation['name'],
                            'address' => $translation['address'] ?? null,
                        ]
                    );
                }
            }

            return $location->load('translations');
        });
    }

    public function delete(Location $location): void
    {
        $this->sortOrderService->deleteAndShift($location);
    }
}
