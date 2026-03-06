<?php

namespace Modules\Rental\Services;

use Illuminate\Support\Facades\DB;
use Modules\Rental\Models\Badge;

class BadgeService
{
    public function __construct(
        protected SortOrderService $sortOrderService,
    ) {}

    public function create(array $data): Badge
    {
        return DB::transaction(function () use ($data) {
            $sortOrder = $this->sortOrderService->insertAtPosition(
                Badge::class,
                $data['sort_order'] ?? null,
            );

            $badge = Badge::create([
                'color' => $data['color'] ?? null,
                'sort_order' => $sortOrder,
                'is_active' => $data['is_active'] ?? true,
            ]);

            foreach ($data['translations'] as $locale => $translation) {
                if (empty($translation['name'])) {
                    continue;
                }

                $badge->translations()->create([
                    'locale' => $locale,
                    'name' => $translation['name'],
                ]);
            }

            return $badge->load('translations');
        });
    }

    public function update(Badge $badge, array $data): Badge
    {
        return DB::transaction(function () use ($badge, $data) {
            $sortOrder = $badge->sort_order;

            if (isset($data['sort_order']) && (int) $data['sort_order'] !== $badge->sort_order) {
                $sortOrder = $this->sortOrderService->moveToPosition(
                    $badge,
                    $data['sort_order'],
                );
            }

            $badge->update([
                'color' => $data['color'] ?? $badge->color,
                'sort_order' => $sortOrder,
                'is_active' => $data['is_active'] ?? $badge->is_active,
            ]);

            if (isset($data['translations'])) {
                foreach ($data['translations'] as $locale => $translation) {
                    if (empty($translation['name'])) {
                        continue;
                    }

                    $badge->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['name' => $translation['name']]
                    );
                }
            }

            return $badge->load('translations');
        });
    }

    public function delete(Badge $badge): void
    {
        $this->sortOrderService->deleteAndShift($badge);
    }
}
