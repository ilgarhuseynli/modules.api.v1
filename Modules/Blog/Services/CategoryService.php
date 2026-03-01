<?php

namespace Modules\Blog\Services;

use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Blog\Models\Category;

class CategoryService
{
    public function __construct(
        protected SortOrderService $sortOrderService,
        protected FileService $fileService,
    ) {}

    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $sortOrder = $this->sortOrderService->insertAtPosition(
                Category::class,
                $data['sort_order'] ?? null,
            );

            $category = Category::create([
                'parent_id' => $data['parent_id'] ?? null,
                'sort_order' => $sortOrder,
                'is_active' => $data['is_active'] ?? true,
                'image_id' => $data['image_id'] ?? null,
            ]);

            foreach ($data['translations'] as $locale => $translation) {
                if (empty($translation['name'])) {
                    continue;
                }

                $category->translations()->create([
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'slug' => Str::slug($translation['name']),
                    'description' => $translation['description'] ?? null,
                ]);
            }


            if (! empty($data['image_id'])) {
                try {
                    $fileData = $this->fileService->storeTmpFile($category,$data['image_id'],'image');
                    $category->update(['image_id' => $fileData['id']]);
                }catch (\Exception $exception){
                    //skip
                }
            }


            return $category->load('translations');
        });
    }

    public function update(Category $category, array $data): Category
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
                'parent_id' => $data['parent_id'] ?? $category->parent_id,
                'sort_order' => $sortOrder,
                'is_active' => $data['is_active'] ?? $category->is_active,
                'image_id' => $data['image_id'] ?? $category->image_id,
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
                            'description' => $translation['description'] ?? null,
                        ]
                    );
                }
            }

            return $category->load('translations');
        });
    }

    public function delete(Category $category): void
    {
        $this->sortOrderService->deleteAndShift($category);
    }
}
