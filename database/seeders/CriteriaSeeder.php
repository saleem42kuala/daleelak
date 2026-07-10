<?php

namespace Database\Seeders;

use App\Models\Criteria;
use Illuminate\Database\Seeder;

class CriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = [
            ['key' => 'halal', 'name_ar' => 'حلال', 'name_en' => 'Halal', 'icon' => 'halal', 'sort_order' => 1],
            ['key' => 'prayer_room', 'name_ar' => 'غرفة صلاة', 'name_en' => 'Prayer Room', 'icon' => 'prayer-room', 'sort_order' => 2],
            ['key' => 'family_section', 'name_ar' => 'قسم عائلي', 'name_en' => 'Family Section', 'icon' => 'family-section', 'sort_order' => 3],
            ['key' => 'arabic_staff', 'name_ar' => 'طاقم يتحدث العربية', 'name_en' => 'Arabic-Speaking Staff', 'icon' => 'arabic-staff', 'sort_order' => 4],
            ['key' => 'alcohol_free', 'name_ar' => 'خالٍ من الكحول', 'name_en' => 'Alcohol-Free', 'icon' => 'alcohol-free', 'sort_order' => 5],
            ['key' => 'modest_friendly', 'name_ar' => 'مناسب للباس المحتشم', 'name_en' => 'Modest-Friendly', 'icon' => 'modest-friendly', 'sort_order' => 6],
        ];

        foreach ($criteria as $item) {
            Criteria::updateOrCreate(['key' => $item['key']], $item);
        }
    }
}
