<?php

namespace Modules\Blog\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Blog\Models\Post;

class PostService
{
    public function create(array $data): Post
    {
        return DB::transaction(function () use ($data) {
            $post = Post::create([
                'author_id' => $data['author_id'],
                'status' => $data['status'] ?? 1,
                'cover_image_id' => $data['cover_image_id'] ?? null,
                'is_featured' => $data['is_featured'] ?? false,
                'published_at' => $data['published_at'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            foreach ($data['translations'] as $locale => $translation) {
                $post->translations()->create([
                    'locale' => $locale,
                    'title' => $translation['title'],
                    'slug' => Str::slug($translation['title']),
                    'excerpt' => $translation['excerpt'] ?? null,
                    'content' => $translation['content'],
                ]);
            }

            if (! empty($data['category_ids'])) {
                $post->categories()->sync($data['category_ids']);
            }

            return $post->load('translations', 'categories');
        });
    }

    public function update(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {
            $post->update([
                'status' => $data['status'] ?? $post->status,
                'cover_image_id' => $data['cover_image_id'] ?? $post->cover_image_id,
                'is_featured' => $data['is_featured'] ?? $post->is_featured,
                'published_at' => $data['published_at'] ?? $post->published_at,
                'sort_order' => $data['sort_order'] ?? $post->sort_order,
            ]);

            if (isset($data['translations'])) {
                foreach ($data['translations'] as $locale => $translation) {
                    $post->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'title' => $translation['title'],
                            'slug' => Str::slug($translation['title']),
                            'excerpt' => $translation['excerpt'] ?? null,
                            'content' => $translation['content'],
                        ]
                    );
                }
            }

            if (array_key_exists('category_ids', $data)) {
                $post->categories()->sync($data['category_ids'] ?? []);
            }

            return $post->load('translations', 'categories');
        });
    }

    public function delete(Post $post): void
    {
        $post->delete();
    }
}
