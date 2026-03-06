<?php

namespace Modules\Rental\Services;

use Illuminate\Support\Facades\DB;
use Modules\Rental\Models\Extra;

class ExtraService
{
    public function __construct(
        protected SortOrderService $sortOrderService,
    ) {}

    public function create(array $data): Extra
    {
        return DB::transaction(function () use ($data) {
            $sortOrder = $this->sortOrderService->insertAtPosition(
                Extra::class,
                $data['sort_order'] ?? null,
            );

            $extra = Extra::create([
                'price' => $data['price'],
                'price_type' => $data['price_type'] ?? 1,
                'is_global' => $data['is_global'] ?? true,
                'is_active' => $data['is_active'] ?? true,
                'sort_order' => $sortOrder,
            ]);

            foreach ($data['translations'] as $locale => $translation) {
                if (empty($translation['name'])) {
                    continue;
                }

                $extra->translations()->create([
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }

            return $extra->load('translations');
        });
    }

    public function update(Extra $extra, array $data): Extra
    {
        return DB::transaction(function () use ($extra, $data) {
            $sortOrder = $extra->sort_order;

            if (isset($data['sort_order']) && (int) $data['sort_order'] !== $extra->sort_order) {
                $sortOrder = $this->sortOrderService->moveToPosition(
                    $extra,
                    $data['sort_order'],
                );
            }

            $extra->update([
                'price' => $data['price'] ?? $extra->price,
                'price_type' => $data['price_type'] ?? $extra->price_type,
                'is_global' => $data['is_global'] ?? $extra->is_global,
                'is_active' => $data['is_active'] ?? $extra->is_active,
                'sort_order' => $sortOrder,
            ]);

            if (isset($data['translations'])) {
                foreach ($data['translations'] as $locale => $translation) {
                    if (empty($translation['name'])) {
                        continue;
                    }

                    $extra->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $translation['name'],
                            'description' => $translation['description'] ?? null,
                        ]
                    );
                }
            }

            return $extra->load('translations');
        });
    }

    public function delete(Extra $extra): void
    {
        $this->sortOrderService->deleteAndShift($extra);
    }
}
