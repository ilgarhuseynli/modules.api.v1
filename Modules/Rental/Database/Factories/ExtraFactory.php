<?php

namespace Modules\Rental\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Rental\Enums\PriceType;
use Modules\Rental\Models\Extra;

class ExtraFactory extends Factory
{
    protected $model = Extra::class;

    public function definition(): array
    {
        return [
            'price' => fake()->randomFloat(2, 5, 100),
            'price_type' => fake()->randomElement(PriceType::cases()),
            'is_global' => true,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function perDay(): static
    {
        return $this->state(fn () => ['price_type' => PriceType::PER_DAY]);
    }

    public function flat(): static
    {
        return $this->state(fn () => ['price_type' => PriceType::FLAT]);
    }
}
