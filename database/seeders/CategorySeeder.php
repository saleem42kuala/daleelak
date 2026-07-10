<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['key' => 'restaurant', 'name_ar' => 'مطعم', 'name_en' => 'Restaurant'],
            ['key' => 'tourism_company', 'name_ar' => 'شركة سياحية', 'name_en' => 'Tourism Company'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['key' => $category['key']], $category);
        }
    }
}
