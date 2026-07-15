<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ListingCriteria;
use App\Models\Review;
use Illuminate\Database\Seeder;

/**
 * Demo data: 15 real/realistic listings across Istanbul, Kuala Lumpur and
 * Sarajevo, each with criteria scores and a handful of approved Arabic reviews.
 *
 * Schema notes (verified against the migrations):
 *  - Listings reference `area_id`, not `city_id`. Each of the three cities has
 *    a single "center" area: Istanbul → 1, Kuala Lumpur → 11, Sarajevo → 19.
 *  - There is no `price_range` column, so the requested price_range (1-4) is
 *    omitted. There is no `status` column either; "active" is `is_active = true`.
 *  - `listing_criteria` uses `score` (decimal) + `votes_count`.
 *  - Reviews have a unique (listing_id, user_id) constraint, so each listing
 *    draws a distinct set of users from the pool.
 *
 * Idempotent: re-running updates the same rows instead of duplicating them.
 */
class RealListingsSeeder extends Seeder
{
    /** criteria ids: halal=1, prayer_room=2, family_section=3, arabic_staff=4, alcohol_free=5, modest_friendly=6 */
    private const CRITERIA_IDS = [1, 2, 3, 4, 5, 6];

    public function run(): void
    {
        $listings = $this->listings();
        $restaurantComments = $this->restaurantComments();
        $tourismComments = $this->tourismComments();
        $userPool = range(2, 31); // 30 non-admin users
        $userCount = count($userPool);

        $reviewsCreated = 0;

        foreach ($listings as $i => $def) {
            $listing = Listing::firstOrCreate(
                ['name_ar' => $def['name_ar'], 'area_id' => $def['area_id']],
                [
                    'category_id' => $def['category_id'],
                    'name_en' => $def['name_en'],
                    'description_ar' => $def['description_ar'],
                    'address_ar' => $def['address_ar'],
                    'phone' => $def['phone'],
                    'latitude' => $def['latitude'],
                    'longitude' => $def['longitude'],
                    'is_active' => true,
                ],
            );

            // Criteria scores for all 6 criteria.
            foreach (self::CRITERIA_IDS as $criteriaId) {
                ListingCriteria::updateOrCreate(
                    ['listing_id' => $listing->id, 'criteria_id' => $criteriaId],
                    ['score' => rand(60, 100), 'votes_count' => rand(3, 15)],
                );
            }

            // 3-5 approved reviews with distinct users and ratings 3-5.
            $reviewCount = 3 + ($i % 3); // 3, 4, 5, 3, 4, 5, ...
            $comments = $def['category_id'] === 1 ? $restaurantComments : $tourismComments;
            $offset = ($i * 5) % $userCount;

            for ($j = 0; $j < $reviewCount; $j++) {
                $userId = $userPool[($offset + $j) % $userCount];
                Review::updateOrCreate(
                    ['listing_id' => $listing->id, 'user_id' => $userId],
                    [
                        'rating' => 3 + (($i + $j) % 3), // 3, 4, 5
                        'comment_ar' => $comments[($i * 5 + $j) % count($comments)],
                        'status' => 'approved',
                        'moderated_at' => now(),
                    ],
                );
                $reviewsCreated++;
            }

            // Refresh overall_rating / reviews_count from the approved reviews.
            $listing->recalculateRatings();
        }

        $this->command->info(
            'RealListingsSeeder done: '.count($listings).' listings, '
            .(count($listings) * count(self::CRITERIA_IDS)).' criteria rows, '
            .$reviewsCreated.' reviews.'
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function listings(): array
    {
        return [
            // ---------------- Istanbul (area_id = 1) ----------------
            [
                'category_id' => 1,
                'area_id' => 1,
                'name_ar' => 'مطعم حمدي',
                'name_en' => 'Hamdi Restaurant',
                'description_ar' => 'مطعم تركي عريق يقع في منطقة إمينونو المطلة على القرن الذهبي، ويشتهر بأطباق الكباب واللحوم المشوية على الطريقة التركية الأصيلة. يوفر إطلالات خلابة على جسر غلطة ومسجد السليمانية مع أجواء عائلية مريحة.',
                'address_ar' => 'كاليتش صوكاك رقم 17، إمينونو، الفاتح، إسطنبول',
                'phone' => '+90 212 528 0390',
                'latitude' => 41.0175,
                'longitude' => 28.9705,
            ],
            [
                'category_id' => 1,
                'area_id' => 1,
                'name_ar' => 'مطعم سلطان أحمد للكفتة',
                'name_en' => 'Sultanahmet Koftecisi',
                'description_ar' => 'مطعم تاريخي في قلب منطقة السلطان أحمد يقدم الكفتة التركية الشهيرة منذ عام 1920. يتميز بموقعه القريب من المسجد الأزرق وآيا صوفيا، ويعد وجهة مثالية للمسافرين العرب الباحثين عن مأكولات حلال.',
                'address_ar' => 'ديوان يولو جادّيسي رقم 12، السلطان أحمد، الفاتح، إسطنبول',
                'phone' => '+90 212 520 0566',
                'latitude' => 41.0086,
                'longitude' => 28.9772,
            ],
            [
                'category_id' => 1,
                'area_id' => 1,
                'name_ar' => 'مطعم بلقان للمأكولات الشرقية',
                'name_en' => 'Balkan Oriental Restaurant',
                'description_ar' => 'مطعم شرقي يقدم تشكيلة واسعة من الأطباق العربية والتركية في منطقة تقسيم الحيوية. يشتهر بالمشاوي والمقبلات الطازجة وحسن الضيافة، ويضم قسمًا مخصصًا للعائلات.',
                'address_ar' => 'شارع الاستقلال، تقسيم، بيوغلو، إسطنبول',
                'phone' => '+90 212 245 6060',
                'latitude' => 41.0369,
                'longitude' => 28.9850,
            ],
            [
                'category_id' => 2,
                'area_id' => 1,
                'name_ar' => 'شركة إسطنبول جولدن للسياحة',
                'name_en' => 'Istanbul Golden Tourism',
                'description_ar' => 'شركة سياحية متخصصة في تنظيم الجولات والرحلات للمسافرين العرب في إسطنبول وعموم تركيا. توفر مرشدين يتحدثون العربية وباقات شاملة تضم الإقامة والتنقل وزيارة أبرز المعالم.',
                'address_ar' => 'شارع الاستقلال رقم 88، بيوغلو، إسطنبول',
                'phone' => '+90 212 292 1010',
                'latitude' => 41.0335,
                'longitude' => 28.9779,
            ],
            [
                'category_id' => 2,
                'area_id' => 1,
                'name_ar' => 'شركة البسفور لرحلات القوارب',
                'name_en' => 'Bosphorus Cruises',
                'description_ar' => 'شركة رحلات بحرية تنظم جولات ممتعة في مضيق البسفور تجمع بين القارتين الآسيوية والأوروبية. تقدم رحلات نهارية ومسائية مع وجبات عشاء وإطلالات ساحرة على قصر دولمة بهجة والقلاع التاريخية.',
                'address_ar' => 'رصيف القوارب، إمينونو، الفاتح، إسطنبول',
                'phone' => '+90 216 418 2020',
                'latitude' => 41.0201,
                'longitude' => 28.9740,
            ],

            // ------------- Kuala Lumpur (area_id = 11) -------------
            [
                'category_id' => 1,
                'area_id' => 11,
                'name_ar' => 'مطعم نسمة العربية',
                'name_en' => 'Naseem Arabian Restaurant',
                'description_ar' => 'مطعم عربي أصيل في قلب منطقة بوكيت بينتانج يقدم أشهى المأكولات الخليجية والشامية. يشتهر بالمندي والكبسة والمشاوي، ويوفر أجواءً عائلية مريحة مع طاقم يتحدث العربية.',
                'address_ar' => 'جالان بوكيت بينتانج، كوالالمبور',
                'phone' => '+60 3-2141 8080',
                'latitude' => 3.1478,
                'longitude' => 101.7107,
            ],
            [
                'category_id' => 1,
                'area_id' => 11,
                'name_ar' => 'مطعم الديار الشامية',
                'name_en' => 'Al-Diyar Levantine',
                'description_ar' => 'مطعم يقدم المطبخ الشامي بنكهات دمشقية أصيلة في شارع العرب الشهير بكوالالمبور. يتميز بالفتوش والحمص والشاورما، ويعد ملتقى للعائلات العربية الزائرة لماليزيا.',
                'address_ar' => 'جالان برجا، بوكيت بينتانج، كوالالمبور',
                'phone' => '+60 3-2148 6633',
                'latitude' => 3.1485,
                'longitude' => 101.7090,
            ],
            [
                'category_id' => 1,
                'area_id' => 11,
                'name_ar' => 'مطعم سماء',
                'name_en' => 'Sama Cuisine',
                'description_ar' => 'مطعم يمزج بين المأكولات الماليزية والعربية بالقرب من برجي بتروناس التوأم. يقدم أطباقًا حلالًا متنوعة في أجواء راقية تناسب العائلات ورجال الأعمال.',
                'address_ar' => 'منطقة KLCC، كوالالمبور',
                'phone' => '+60 3-2382 0000',
                'latitude' => 3.1570,
                'longitude' => 101.7123,
            ],
            [
                'category_id' => 2,
                'area_id' => 11,
                'name_ar' => 'شركة ماليزيا تروبيكال للسياحة',
                'name_en' => 'Malaysia Tropical Tours',
                'description_ar' => 'شركة سياحية رائدة تنظم برامج متكاملة لاكتشاف ماليزيا من كوالالمبور إلى لنكاوي وبينانج. توفر مرشدين عرب وباقات عائلية تشمل الجولات والفنادق والمواصلات.',
                'address_ar' => 'جالان بوكيت بينتانج، كوالالمبور',
                'phone' => '+60 3-2110 3030',
                'latitude' => 3.1466,
                'longitude' => 101.7113,
            ],
            [
                'category_id' => 2,
                'area_id' => 11,
                'name_ar' => 'شركة كوالالمبور للجولات السياحية',
                'name_en' => 'KL City Tours',
                'description_ar' => 'شركة متخصصة في الجولات داخل مدينة كوالالمبور تشمل زيارة برجي بتروناس وكهوف باتو والحدائق الوطنية. تقدم رحلات يومية بمرشدين محترفين وخدمة نقل مريحة.',
                'address_ar' => 'منطقة KLCC، كوالالمبور',
                'phone' => '+60 3-2166 4040',
                'latitude' => 3.1579,
                'longitude' => 101.7118,
            ],

            // --------------- Sarajevo (area_id = 19) ---------------
            [
                'category_id' => 1,
                'area_id' => 19,
                'name_ar' => 'مطعم شيفابي البلقان',
                'name_en' => 'Balkan Cevabdzinica',
                'description_ar' => 'مطعم بوسني تقليدي في حي باشتشارشيا التاريخي يقدم الشيفابي الشهير مع الخبز الطازج والبصل. يتميز بأجوائه العتيقة ولحومه الحلال التي تجذب السياح العرب.',
                'address_ar' => 'حي باشتشارشيا، سراييفو',
                'phone' => '+387 33 447 000',
                'latitude' => 43.8595,
                'longitude' => 18.4310,
            ],
            [
                'category_id' => 1,
                'area_id' => 19,
                'name_ar' => 'مطعم البلدة القديمة',
                'name_en' => 'Old Town Restaurant',
                'description_ar' => 'مطعم يقع في البلدة القديمة بسراييفو ويقدم أطباقًا بوسنية وشرقية في أجواء تراثية. يشتهر بطبق البوريك واليخنات البوسنية، ويوفر قائمة حلال متكاملة.',
                'address_ar' => 'شارع فرهاديا، باشتشارشيا، سراييفو',
                'phone' => '+387 33 535 353',
                'latitude' => 43.8590,
                'longitude' => 18.4295,
            ],
            [
                'category_id' => 1,
                'area_id' => 19,
                'name_ar' => 'مطعم الشرق الأوسط',
                'name_en' => 'Middle East Restaurant',
                'description_ar' => 'مطعم عربي في وسط سراييفو يقدم المأكولات الخليجية والشامية للجالية والسياح العرب. يتميز بالمندي والمشاوي وحسن الضيافة مع قسم عائلي هادئ.',
                'address_ar' => 'شارع تيتوفا، وسط سراييفو',
                'phone' => '+387 33 200 100',
                'latitude' => 43.8563,
                'longitude' => 18.4131,
            ],
            [
                'category_id' => 2,
                'area_id' => 19,
                'name_ar' => 'شركة سراييفو للسياحة والسفر',
                'name_en' => 'Sarajevo Travel',
                'description_ar' => 'شركة سياحية تنظم جولات لاكتشاف معالم سراييفو والبوسنة والهرسك من الشلالات إلى الجبال. توفر مرشدين يتحدثون العربية وبرامج مصممة للعائلات الخليجية.',
                'address_ar' => 'حي باشتشارشيا، سراييفو',
                'phone' => '+387 33 222 333',
                'latitude' => 43.8586,
                'longitude' => 18.4256,
            ],
            [
                'category_id' => 2,
                'area_id' => 19,
                'name_ar' => 'شركة البوسنة الخضراء للرحلات',
                'name_en' => 'Green Bosnia Tours',
                'description_ar' => 'شركة متخصصة في السياحة البيئية والرحلات الجبلية في البوسنة والهرسك. تقدم برامج لزيارة شلالات كرافيتسا ونهر نيريتفا ومدينة موستار بمرشدين محترفين.',
                'address_ar' => 'شارع مارشالا تيتا، وسط سراييفو',
                'phone' => '+387 33 555 777',
                'latitude' => 43.8519,
                'longitude' => 18.3867,
            ],
        ];
    }

    /** @return array<int, string> */
    private function restaurantComments(): array
    {
        return [
            'تجربة رائعة، الطعام لذيذ والخدمة ممتازة. أنصح بزيارته بشدة.',
            'المكان نظيف والأجواء عائلية مريحة، سنعود مرة أخرى بإذن الله.',
            'أطباق شهية وأسعار مناسبة، والطاقم متعاون ويتحدث العربية.',
            'استمتعنا كثيرًا بالزيارة، اللحوم طازجة والنكهة أصيلة.',
            'خدمة سريعة واهتمام بالتفاصيل، من أفضل الأماكن التي زرناها.',
            'الموقع مميز وسهل الوصول، والطعام كان في مستوى توقعاتنا.',
            'تعامل راقٍ وضيافة كريمة، والمكان مناسب جدًا للعائلات.',
            'جودة عالية ونظافة ممتازة، تجربة تستحق التكرار.',
            'أنصح المسافرين العرب بتجربة هذا المكان، لن تندموا.',
            'الأجواء جميلة والطعام حلال ولذيذ، شكرًا على حسن الاستقبال.',
        ];
    }

    /** @return array<int, string> */
    private function tourismComments(): array
    {
        return [
            'تنظيم ممتاز للرحلة والمرشد كان متعاونًا ويتحدث العربية بطلاقة.',
            'برنامج سياحي رائع وأسعار معقولة، أنصح بالتعامل معهم.',
            'رحلة منظمة ومريحة، اهتموا بأدق التفاصيل طوال الجولة.',
            'خدمة احترافية وتعامل راقٍ، استمتعنا بكل لحظة في الرحلة.',
            'المرشد السياحي كان على مستوى عالٍ من المعرفة والخبرة.',
            'تجربة سياحية لا تُنسى وباقات مناسبة للعائلات.',
            'مواعيد دقيقة وتنظيم رائع، سنتعامل معهم في زيارتنا القادمة.',
            'أنصح بهم لكل مسافر عربي يبحث عن رحلة مريحة ومنظمة.',
            'خدمة نقل مريحة وجولات ممتعة، شكرًا على حسن التعامل.',
            'برنامج شامل وأسعار تنافسية، تجربة تستحق التوصية بها.',
        ];
    }
}
