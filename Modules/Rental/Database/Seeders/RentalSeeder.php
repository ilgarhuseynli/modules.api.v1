<?php

namespace Modules\Rental\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Rental\Enums\PriceType;
use Modules\Rental\Models\Badge;
use Modules\Rental\Models\Car;
use Modules\Rental\Models\CarCategory;
use Modules\Rental\Models\Extra;
use Modules\Rental\Models\Location;

class RentalSeeder extends Seeder
{
    public function run(): void
    {
        $locales = ['az', 'en', 'ru'];

        // --- Car Categories ---
        $categoryData = [
            ['az' => 'Ekonom', 'en' => 'Economy', 'ru' => 'Эконом'],
            ['az' => 'Biznes', 'en' => 'Business', 'ru' => 'Бизнес'],
            ['az' => 'Premium', 'en' => 'Premium', 'ru' => 'Премиум'],
            ['az' => 'SUV', 'en' => 'SUV', 'ru' => 'Внедорожник'],
            ['az' => 'Mikroavtobus', 'en' => 'Minivan', 'ru' => 'Минивэн'],
        ];

        $categories = [];
        foreach ($categoryData as $index => $names) {
            $category = CarCategory::create([
                'is_active' => true,
                'sort_order' => $index,
            ]);

            foreach ($locales as $locale) {
                $category->translations()->create([
                    'locale' => $locale,
                    'name' => $names[$locale],
                    'slug' => Str::slug($names[$locale]),
                ]);
            }

            $categories[] = $category;
        }

        // --- Badges ---
        $badgeData = [
            ['az' => 'Yeni', 'en' => 'New', 'ru' => 'Новый', 'color' => '#22c55e'],
            ['az' => 'Populyar', 'en' => 'Popular', 'ru' => 'Популярный', 'color' => '#f59e0b'],
            ['az' => 'Endirim', 'en' => 'Sale', 'ru' => 'Скидка', 'color' => '#ef4444'],
            ['az' => 'Tövsiyə edilir', 'en' => 'Recommended', 'ru' => 'Рекомендуется', 'color' => '#3b82f6'],
        ];

        $badges = [];
        foreach ($badgeData as $index => $data) {
            $badge = Badge::create([
                'color' => $data['color'],
                'is_active' => true,
                'sort_order' => $index,
            ]);

            foreach ($locales as $locale) {
                $badge->translations()->create([
                    'locale' => $locale,
                    'name' => $data[$locale],
                ]);
            }

            $badges[] = $badge;
        }

        // --- Extras ---
        $extraData = [
            ['az' => 'Uşaq oturacağı', 'en' => 'Child Seat', 'ru' => 'Детское кресло', 'price' => 10.00, 'price_type' => PriceType::PER_DAY],
            ['az' => 'GPS naviqasiya', 'en' => 'GPS Navigation', 'ru' => 'GPS навигация', 'price' => 5.00, 'price_type' => PriceType::PER_DAY],
            ['az' => 'Əlavə sürücü', 'en' => 'Additional Driver', 'ru' => 'Дополнительный водитель', 'price' => 15.00, 'price_type' => PriceType::FLAT],
            ['az' => 'Tam sığorta', 'en' => 'Full Insurance', 'ru' => 'Полная страховка', 'price' => 20.00, 'price_type' => PriceType::PER_DAY],
            ['az' => 'WiFi router', 'en' => 'WiFi Router', 'ru' => 'WiFi роутер', 'price' => 8.00, 'price_type' => PriceType::PER_DAY],
        ];

        $extras = [];
        foreach ($extraData as $index => $data) {
            $extra = Extra::create([
                'price' => $data['price'],
                'price_type' => $data['price_type'],
                'is_global' => true,
                'is_active' => true,
                'sort_order' => $index,
            ]);

            foreach ($locales as $locale) {
                $extra->translations()->create([
                    'locale' => $locale,
                    'name' => $data[$locale],
                ]);
            }

            $extras[] = $extra;
        }

        // --- Locations ---
        $locationData = [
            ['az' => 'Bakı ofisi', 'en' => 'Baku Office', 'ru' => 'Офис Баку', 'address_en' => 'Ahmad Rajabli 2/27', 'price' => null],
            ['az' => 'Ağ Şəhər ofisi', 'en' => 'White City Office', 'ru' => 'Офис Белый Город', 'address_en' => 'Center, Boulevard Street 3', 'price' => null],
            ['az' => 'Bakı GYD Hava Limanı', 'en' => 'Airport Baku GYD', 'ru' => 'Аэропорт Баку GYD', 'address_en' => 'Heydar Aliyev International Airport', 'price' => 20.00],
            ['az' => 'Bakı şəhər daxili', 'en' => 'Inside Baku City', 'ru' => 'В черте Баку', 'address_en' => 'Baku city limits', 'price' => 10.00],
            ['az' => 'Winterpark Otel', 'en' => 'Baku Winterpark Hotel', 'ru' => 'Отель Baku Winterpark', 'address_en' => 'Heydar Aliyev Ave 155', 'price' => 10.00],
            ['az' => 'Courtyard by Marriott', 'en' => 'Baku Courtyard by Marriott', 'ru' => 'Baku Courtyard by Marriott', 'address_en' => 'Azneft Square 1', 'price' => 10.00],
            ['az' => 'Hilton Otel', 'en' => 'Baku Hilton Hotel', 'ru' => 'Отель Hilton Баку', 'address_en' => 'Istiglaliyyat 1', 'price' => 10.00],
        ];

        foreach ($locationData as $index => $data) {
            $location = Location::create([
                'price' => $data['price'],
                'is_active' => true,
                'sort_order' => $index,
            ]);

            foreach ($locales as $locale) {
                $location->translations()->create([
                    'locale' => $locale,
                    'name' => $data[$locale],
                    'address' => $data['address_en'],
                ]);
            }
        }

        // --- Cars ---
        Car::factory(20)->create()->each(function (Car $car) use ($badges, $extras) {
            // Attach 0–2 random badges
            $car->badges()->attach(
                collect($badges)->random(fake()->numberBetween(0, 2))->pluck('id')
            );

            // Attach 1–3 random extras (with optional price overrides)
            $car->extras()->attach(
                collect($extras)->random(fake()->numberBetween(1, 3))->pluck('id')
            );

            // Add translations
            foreach (['az', 'en', 'ru'] as $locale) {
                $car->translations()->create([
                    'locale' => $locale,
                    'description' => "{$car->brand} {$car->model} — comfortable and reliable rental car.",
                ]);
            }
        });
    }
}
