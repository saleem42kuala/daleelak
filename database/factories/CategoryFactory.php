<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
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
        ];
    }
}
