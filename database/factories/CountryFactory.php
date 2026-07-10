<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_ar' => fake()->unique()->country(),
            'name_en' => fake('en_US')->country(),
            'code' => fake()->unique()->countryCode(),
            'phone_code' => '+'.fake()->numberBetween(1, 999),
            'flag_emoji' => null,
        ];
    }
}
