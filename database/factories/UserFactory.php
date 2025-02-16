<?php

namespace Database\Factories;

use App\Classes\Helpers;
use App\Enums\UserGender;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $first_name = $this->faker->firstName();
        $last_name = $this->faker->lastName();
        $full_name = $first_name . ' ' . $last_name;

        $phones = [
            $this->generatePhone(),
            $this->generatePhone(),
        ];

        $email = fake()->unique()->safeEmail();

        //concat full name slug, phones, email to generate keyword
        $keyword = Str::slug($full_name, ' ');
        $keyword .= implode(' ',$phones);
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
            'type' => fake()->randomElement(UserType::getValues()),
            'avatar' => fake()->imageUrl(200, 200, 'people'),
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
            'two_factor_enabled' => false,
            'two_factor_verified_at' => null,
        ];
    }


    public function generatePhone()
    {
        $phone = '+994' . fake()->randomElement(['50', '51', '55', '70', '77', '99']) .
            fake()->numerify('#######');

        return Helpers::filterPhone($phone);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
