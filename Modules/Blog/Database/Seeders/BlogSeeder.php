<?php

namespace Modules\Blog\Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Blog\Enums\PostStatus;
use Modules\Blog\Models\Category;
use Modules\Blog\Models\Post;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $locales = ['az', 'en', 'ru'];

        $categoryNames = [
            'en' => ['News', 'Tips & Tricks', 'Travel', 'Car Reviews'],
            'az' => ['Xəbərlər', 'Məsləhətlər', 'Səyahət', 'Avtomobil İcmalları'],
            'ru' => ['Новости', 'Советы', 'Путешествия', 'Обзоры автомобилей'],
        ];

        $categories = [];
        foreach ($categoryNames['en'] as $index => $name) {
            $category = Category::create([
                'sort_order' => $index,
                'is_active' => true,
            ]);

            foreach ($locales as $locale) {
                $translatedName = $categoryNames[$locale][$index];
                $category->translations()->create([
                    'locale' => $locale,
                    'name' => $translatedName,
                    'slug' => Str::slug($translatedName),
                    'description' => "Description for {$translatedName}",
                ]);
            }

            $categories[] = $category;
        }

        $author = User::where('type', UserType::EMPLOYEE)->first();

        if (! $author) {
            return;
        }

        $postTitles = [
            'en' => ['Best Road Trips in Azerbaijan', 'How to Choose a Rental Car', 'Top 5 SUVs for 2026'],
            'az' => ['Azərbaycanda Ən Yaxşı Yol Səyahətləri', 'İcarə Avtomobili Necə Seçmək Olar', '2026-nın Ən Yaxşı 5 SUV-u'],
            'ru' => ['Лучшие автопутешествия по Азербайджану', 'Как выбрать арендный автомобиль', 'Топ-5 внедорожников 2026 года'],
        ];

        foreach ($postTitles['en'] as $index => $title) {
            $post = Post::create([
                'author_id' => $author->id,
                'status' => PostStatus::PUBLISHED,
                'is_featured' => $index === 0,
                'published_at' => now()->subDays($index),
                'sort_order' => $index,
            ]);

            foreach ($locales as $locale) {
                $translatedTitle = $postTitles[$locale][$index];
                $post->translations()->create([
                    'locale' => $locale,
                    'title' => $translatedTitle,
                    'slug' => Str::slug($translatedTitle),
                    'excerpt' => "Excerpt for {$translatedTitle}",
                    'content' => "Full content for {$translatedTitle}. Lorem ipsum dolor sit amet.",
                ]);
            }

            $post->categories()->attach($categories[array_rand($categories)]->id);
        }
    }
}
