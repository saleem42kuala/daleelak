<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Restaurant listing names.
     *
     * @var array<int, string>
     */
    private array $restaurantNames = [
        'مطعم الأصالة الشامية',
        'مطعم دار الضيافة',
        'مطعم زيتون وزعتر',
        'مطعم قصر المندي',
        'مطعم بيت الشاورما',
        'مطعم النخيل الذهبي',
        'مطعم واحة الذواقة',
        'مطعم أطايب الشرق',
        'مطعم ليالي بيروت',
        'مطعم سنابل الخير',
    ];

    /**
     * Tourism company listing names.
     *
     * @var array<int, string>
     */
    private array $tourismNames = [
        'شركة رحلات الخليج للسياحة',
        'شركة درة الشرق للسياحة والسفر',
        'شركة أجنحة السفر',
        'شركة واحة السياحة والسفر',
        'شركة كنوز الأندلس للسياحة',
        'شركة رحلاتي العالمية للسياحة',
        'شركة سائح الشرق للسياحة',
        'شركة نجوم السفر والسياحة',
        'شركة الأصيل للرحلات السياحية',
        'شركة بوصلة السفر والسياحة',
    ];

    /**
     * Run the database seeds.
     *
     * Requires GeographySeeder to have already run: areas are created one
     * per real city (a generic "city centre" placeholder), since no
     * neighborhood-level area data has been provided yet. Replace with real
     * areas per city when that data is available.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'مدير النظام',
            'email' => 'admin@daleelak.test',
            'password' => bcrypt('password'),
        ]);

        $reviewers = User::factory()->count(30)->create();

        $areas = City::all()->map(fn (City $city) => Area::create([
            'city_id' => $city->id,
            'name_ar' => 'وسط '.$city->name_ar,
        ]));

        $restaurantCategory = Category::where('key', 'restaurant')->firstOrFail();
        $tourismCategory = Category::where('key', 'tourism_company')->firstOrFail();

        $this->createListings($this->restaurantNames, $restaurantCategory, $areas, $reviewers);
        $this->createListings($this->tourismNames, $tourismCategory, $areas, $reviewers);
    }

    /**
     * @param  array<int, string>  $names
     * @param  \Illuminate\Support\Collection<int, Area>  $areas
     * @param  \Illuminate\Database\Eloquent\Collection<int, User>  $reviewers
     */
    private function createListings(array $names, Category $category, $areas, $reviewers): void
    {
        foreach ($names as $name) {
            $listing = Listing::factory()->create([
                'category_id' => $category->id,
                'area_id' => $areas->random()->id,
                'name_ar' => $name,
            ]);

            $reviewerPool = $reviewers->shuffle()->take(fake()->numberBetween(3, 8));

            foreach ($reviewerPool as $reviewer) {
                Review::factory()->create([
                    'listing_id' => $listing->id,
                    'user_id' => $reviewer->id,
                ]);
            }
        }
    }
}
