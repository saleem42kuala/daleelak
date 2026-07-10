<?php

namespace Database\Factories;

use App\Models\Criteria;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Criteria>
 */
class CriteriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake('en_US')->unique()->word();

        return [
            'key' => Str::slug($name, '_'),
            'name_ar' => fake()->word(),
            'name_en' => ucfirst($name),
            'icon' => null,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
