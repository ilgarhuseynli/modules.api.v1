<?php

namespace Modules\Rental\Services;

use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use Modules\Rental\Models\Car;

class CarService
{
    public function __construct(
        protected SortOrderService $sortOrderService,
        protected FileService $fileService,
    ) {}

    public function create(array $data): Car
    {
        return DB::transaction(function () use ($data) {
            $sortOrder = $this->sortOrderService->insertAtPosition(
                Car::class,
                $data['sort_order'] ?? null,
            );

            $car = Car::create([
                'category_id' => $data['category_id'] ?? null,
                'brand' => $data['brand'],
                'model' => $data['model'],
                'year' => $data['year'],
                'plate_number' => $data['plate_number'] ?? null,
                'color' => $data['color'] ?? null,
                'transmission' => $data['transmission'],
                'fuel_type' => $data['fuel_type'],
                'body_type' => $data['body_type'],
                'seats' => $data['seats'] ?? 5,
                'doors' => $data['doors'] ?? 4,
                'engine' => $data['engine'] ?? null,
                'price_daily' => $data['price_daily'],
                'price_weekly' => $data['price_weekly'] ?? null,
                'price_monthly' => $data['price_monthly'] ?? null,
                'deposit' => $data['deposit'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'sort_order' => $sortOrder,
            ]);

            if (! empty($data['translations'])) {
                foreach ($data['translations'] as $locale => $translation) {
                    $car->translations()->create([
                        'locale' => $locale,
                        'description' => $translation['description'] ?? null,
                    ]);
                }
            }

            if (! empty($data['badge_ids'])) {
                $car->badges()->sync($data['badge_ids']);
            }

            if (! empty($data['extra_ids'])) {
                $car->extras()->sync($this->buildExtraSync($data['extra_ids']));
            }

            if (! empty($data['avatar_id'])) {
                try {
                    $fileData = $this->fileService->storeTmpFile($car, $data['avatar_id'], 'avatar');
                    $car->update(['avatar_id' => $fileData['id']]);
                } catch (\Exception $exception) {
                    // skip
                }
            }

            if (! empty($data['images'])) {
                foreach ($data['images'] as $imageId) {
                    try {
                        $this->fileService->storeTmpFile($car, $imageId, 'images');
                    } catch (\Exception $exception) {
                        // skip
                    }
                }
            }

            return $car->load('translations', 'badges', 'category');
        });
    }

    public function update(Car $car, array $data): Car
    {
        return DB::transaction(function () use ($car, $data) {
            $sortOrder = $car->sort_order;

            if (isset($data['sort_order']) && (int) $data['sort_order'] !== $car->sort_order) {
                $sortOrder = $this->sortOrderService->moveToPosition(
                    $car,
                    $data['sort_order'],
                );
            }

            $car->update([
                'category_id' => $data['category_id'] ?? $car->category_id,
                'brand' => $data['brand'] ?? $car->brand,
                'model' => $data['model'] ?? $car->model,
                'year' => $data['year'] ?? $car->year,
                'plate_number' => $data['plate_number'] ?? $car->plate_number,
                'color' => $data['color'] ?? $car->color,
                'transmission' => $data['transmission'] ?? $car->transmission,
                'fuel_type' => $data['fuel_type'] ?? $car->fuel_type,
                'body_type' => $data['body_type'] ?? $car->body_type,
                'seats' => $data['seats'] ?? $car->seats,
                'doors' => $data['doors'] ?? $car->doors,
                'engine' => $data['engine'] ?? $car->engine,
                'price_daily' => $data['price_daily'] ?? $car->price_daily,
                'price_weekly' => $data['price_weekly'] ?? $car->price_weekly,
                'price_monthly' => $data['price_monthly'] ?? $car->price_monthly,
                'deposit' => $data['deposit'] ?? $car->deposit,
                'is_active' => $data['is_active'] ?? $car->is_active,
                'sort_order' => $sortOrder,
            ]);

            if (isset($data['translations'])) {
                foreach ($data['translations'] as $locale => $translation) {
                    $car->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['description' => $translation['description'] ?? null]
                    );
                }
            }

            if (array_key_exists('badge_ids', $data)) {
                $car->badges()->sync($data['badge_ids'] ?? []);
            }

            if (array_key_exists('extra_ids', $data)) {
                $car->extras()->sync($this->buildExtraSync($data['extra_ids'] ?? []));
            }

            return $car->load('translations', 'badges', 'category');
        });
    }

    public function delete(Car $car): void
    {
        $this->sortOrderService->deleteAndShift($car);
    }

    /**
     * Build sync array for extras with optional pivot overrides.
     *
     * Accepts: [1, 2] or [['id' => 1, 'price' => 10, 'price_type' => 2], ...]
     */
    private function buildExtraSync(array $extras): array
    {
        $sync = [];

        foreach ($extras as $extra) {
            if (is_array($extra)) {
                $sync[$extra['id']] = array_filter([
                    'price' => $extra['price'] ?? null,
                    'price_type' => $extra['price_type'] ?? null,
                ], fn ($v) => $v !== null);
            } else {
                $sync[$extra] = [];
            }
        }

        return $sync;
    }
}
