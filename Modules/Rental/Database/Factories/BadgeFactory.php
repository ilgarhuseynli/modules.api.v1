<?php

namespace Modules\Rental\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Rental\Models\Badge;

class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    public function definition(): array
    {
        return [
            'color' => fake()->hexColor(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
