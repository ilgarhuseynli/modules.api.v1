<?php

namespace Modules\Rental\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Rental\Models\CarCategory;

class CarCategoryService
{
    public function __construct(
        protected SortOrderService $sortOrderService,
    ) {}

    public function create(array $data): CarCategory
    {
        return DB::transaction(function () use ($data) {
            $sortOrder = $this->sortOrderService->insertAtPosition(
                CarCategory::class,
                $data['sort_order'] ?? null,
            );

            $category = CarCategory::create([
                'sort_order' => $sortOrder,
                'is_active' => $data['is_active'] ?? true,
            ]);

            foreach ($data['translations'] as $locale => $translation) {
                if (empty($translation['name'])) {
                    continue;
                }

                $category->translations()->create([
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'slug' => Str::slug($translation['name']),
                ]);
            }

            return $category->load('translations');
        });
    }

    public function update(CarCategory $category, array $data): CarCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $sortOrder = $category->sort_order;

            if (isset($data['sort_order']) && (int) $data['sort_order'] !== $category->sort_order) {
                $sortOrder = $this->sortOrderService->moveToPosition(
                    $category,
                    $data['sort_order'],
                );
            }

            $category->update([
                'sort_order' => $sortOrder,
                'is_active' => $data['is_active'] ?? $category->is_active,
            ]);

            if (isset($data['translations'])) {
                foreach ($data['translations'] as $locale => $translation) {
                    if (empty($translation['name'])) {
                        continue;
                    }

                    $category->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $translation['name'],
                            'slug' => Str::slug($translation['name']),
                        ]
                    );
                }
            }

            return $category->load('translations');
        });
    }

    public function delete(CarCategory $category): void
    {
        $this->sortOrderService->deleteAndShift($category);
    }
}
