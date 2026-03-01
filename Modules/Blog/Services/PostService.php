<?php

namespace Modules\Blog\Services;

use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Blog\Models\Post;

class PostService
{
    public function __construct(
        protected SortOrderService $sortOrderService,
        protected FileService $fileService,
    ) {}

    public function create(array $data): Post
    {
        return DB::transaction(function () use ($data) {
            $sortOrder = $this->sortOrderService->insertAtPosition(
                Post::class,
                $data['sort_order'] ?? null,
            );

            $post = Post::create([
                'author_id' => $data['author_id'],
                'status' => $data['status'] ?? 1,
                'cover_image_id' => $data['cover_image_id'] ?? null,
                'is_featured' => $data['is_featured'] ?? false,
                'published_at' => $data['published_at'] ?? null,
                'sort_order' => $sortOrder,
            ]);

            foreach ($data['translations'] as $locale => $translation) {
                if (empty($translation['title']) && empty($translation['content'])) {
                    continue;
                }

                $post->translations()->create([
                    'locale' => $locale,
                    'title' => $translation['title'] ?? '',
                    'slug' => Str::slug($translation['title'] ?? ''),
                    'excerpt' => $translation['excerpt'] ?? null,
                    'content' => $translation['content'] ?? '',
                ]);
            }

            if (! empty($data['category_ids'])) {
                $post->categories()->sync($data['category_ids']);
            }


            if (! empty($data['cover_image_id'])) {

                try {
                    $fileData = $this->fileService->storeTmpFile($post,$data['cover_image_id'],'cover_image');
                    $post->update(['cover_image_id' => $fileData['id']]);
                }catch (\Exception $exception){
                    //skip
                }
            }

            if (! empty($data['images'])) {
                foreach ($data['images'] as $imageId) {
                    try {
                        $fileData = $this->fileService->storeTmpFile($post,$imageId,'images');
                    }catch (\Exception $exception){
                        //skip
                    }
                }
            }

            return $post->load('translations', 'categories');
        });
    }

    public function update(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {
            $sortOrder = $post->sort_order;

            if (isset($data['sort_order']) && (int) $data['sort_order'] !== $post->sort_order) {
                $sortOrder = $this->sortOrderService->moveToPosition(
                    $post,
                    $data['sort_order'],
                );
            }

            $post->update([
                'status' => $data['status'] ?? $post->status,
                'cover_image_id' => $data['cover_image_id'] ?? $post->cover_image_id,
                'is_featured' => $data['is_featured'] ?? $post->is_featured,
                'published_at' => $data['published_at'] ?? $post->published_at,
                'sort_order' => $sortOrder,
            ]);

            if (isset($data['translations'])) {
                foreach ($data['translations'] as $locale => $translation) {
                    if (empty($translation['title']) && empty($translation['content'])) {
                        continue;
                    }

                    $post->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'title' => $translation['title'] ?? '',
                            'slug' => Str::slug($translation['title'] ?? ''),
                            'excerpt' => $translation['excerpt'] ?? null,
                            'content' => $translation['content'] ?? '',
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
        $this->sortOrderService->deleteAndShift($post);
    }
}
