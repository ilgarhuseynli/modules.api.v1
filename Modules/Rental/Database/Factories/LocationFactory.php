<?php

namespace Modules\Rental\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Rental\Models\Location;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'price' => fake()->randomElement([null, null, 10, 15, 20, 25]),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function free(): static
    {
        return $this->state(fn () => ['price' => null]);
    }
}
