<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $descriptions = [
            'وجهة مميزة تجمع بين الأصالة والجودة، مع خدمة تليق بتوقعات الزائر العربي.',
            'مكان يوفر تجربة مريحة للعائلات مع اهتمام خاص بالتفاصيل التي يبحث عنها المسافر العربي.',
            'خيار موثوق يجمع بين الطابع المحلي والمعايير التي يفضلها الزوار من الدول العربية.',
            'تجربة متكاملة تراعي احتياجات الزائر العربي من حيث الراحة والخصوصية والجودة.',
        ];

        return [
            'category_id' => Category::factory(),
            'area_id' => Area::factory(),
            'name_ar' => fake()->company(),
            'name_en' => null,
            'description_ar' => fake()->randomElement($descriptions),
            'address_ar' => fake()->address(),
            'phone' => '05'.fake()->numerify('########'),
            'latitude' => fake()->latitude(12, 32),
            'longitude' => fake()->longitude(-17, 55),
            'overall_rating' => 0,
            'reviews_count' => 0,
            'is_active' => true,
        ];
    }
}
