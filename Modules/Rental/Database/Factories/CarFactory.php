<?php

namespace Modules\Rental\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Rental\Enums\BodyType;
use Modules\Rental\Enums\FuelType;
use Modules\Rental\Enums\Transmission;
use Modules\Rental\Models\Car;
use Modules\Rental\Models\CarCategory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    private static array $carData = [
        ['brand' => 'Toyota', 'model' => 'Camry'],
        ['brand' => 'Toyota', 'model' => 'RAV4'],
        ['brand' => 'Toyota', 'model' => 'Land Cruiser'],
        ['brand' => 'BMW', 'model' => '5 Series'],
        ['brand' => 'BMW', 'model' => 'X5'],
        ['brand' => 'Mercedes-Benz', 'model' => 'E-Class'],
        ['brand' => 'Mercedes-Benz', 'model' => 'GLE'],
        ['brand' => 'Hyundai', 'model' => 'Tucson'],
        ['brand' => 'Hyundai', 'model' => 'Santa Fe'],
        ['brand' => 'Kia', 'model' => 'Sportage'],
        ['brand' => 'Volkswagen', 'model' => 'Passat'],
        ['brand' => 'Audi', 'model' => 'A6'],
    ];

    public function definition(): array
    {
        $car = fake()->randomElement(self::$carData);
        $priceDaily = fake()->randomFloat(2, 40, 200);

        return [
            'category_id' => CarCategory::query()->inRandomOrder()->value('id'),
            'brand' => $car['brand'],
            'model' => $car['model'],
            'year' => fake()->numberBetween(2018, 2025),
            'plate_number' => strtoupper(fake()->bothify('??-###-??')),
            'color' => fake()->colorName(),
            'transmission' => fake()->randomElement(Transmission::cases()),
            'fuel_type' => fake()->randomElement(FuelType::cases()),
            'body_type' => fake()->randomElement(BodyType::cases()),
            'seats' => fake()->randomElement([4, 5, 7]),
            'doors' => fake()->randomElement([2, 4]),
            'engine' => fake()->randomElement(['1.5L', '2.0L', '2.5L', '3.0L', 'Electric']),
            'price_daily' => $priceDaily,
            'price_weekly' => round($priceDaily * 6, 2),
            'price_monthly' => round($priceDaily * 20, 2),
            'deposit' => fake()->randomFloat(2, 100, 500),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
