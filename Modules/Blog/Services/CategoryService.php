<?php

namespace Modules\Blog\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Blog\Models\Category;

class CategoryService
{
    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $category = Category::create([
                'parent_id' => $data['parent_id'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
                'image_id' => $data['image_id'] ?? null,
            ]);

            foreach ($data['translations'] as $locale => $translation) {
                $category->translations()->create([
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'slug' => Str::slug($translation['name']),
                    'description' => $translation['description'] ?? null,
                ]);
            }

            return $category->load('translations');
        });
    }

    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update([
                'parent_id' => $data['parent_id'] ?? $category->parent_id,
                'sort_order' => $data['sort_order'] ?? $category->sort_order,
                'is_active' => $data['is_active'] ?? $category->is_active,
                'image_id' => $data['image_id'] ?? $category->image_id,
            ]);

            if (isset($data['translations'])) {
                foreach ($data['translations'] as $locale => $translation) {
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
        $category->delete();
    }
}
