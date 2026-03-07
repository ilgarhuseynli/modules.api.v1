<?php

namespace Modules\Rental\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Rental\Models\CarCategory;

class CarCategoryFactory extends Factory
{
    protected $model = CarCategory::class;

    public function definition(): array
    {
        return [
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
