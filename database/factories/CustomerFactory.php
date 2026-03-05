<?php

namespace Database\Factories;

use App\Classes\Helpers;
use App\Enums\UserGender;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    protected static ?string $password;

    public function definition(): array
    {
        $first_name = $this->faker->firstName();
        $last_name = $this->faker->lastName();
        $full_name = $first_name.' '.$last_name;

        $phones = [
            $this->generatePhone(),
            $this->generatePhone(),
        ];

        $email = fake()->unique()->safeEmail();

        $keyword = Str::slug($full_name, ' ');
        $keyword .= implode(' ', $phones);
        $keyword .= $email;

        return [
            'name' => $full_name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phones[0],
            'keyword' => $keyword,
            'is_company' => fake()->boolean(),
            'send_notification' => fake()->boolean(),
            'avatar_id' => null,
            'gender' => fake()->randomElement(UserGender::getValues()),
            'birth_date' => fake()->dateTimeBetween(
                now()->subYears(40),
                now()->subYears(18)
            )->format('Y-m-d'),
            'address' => [
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'country' => fake()->country(),
                'postal_code' => fake()->postcode(),
            ],
            'phones' => $phones,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function generatePhone(): string
    {
        $phone = '+994'.fake()->randomElement(['50', '51', '55', '70', '77', '99']).
            fake()->numerify('#######');

        return Helpers::filterPhone($phone);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
