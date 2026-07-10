<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class GeographySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        Schema::disableForeignKeyConstraints();

        // ============================================================
        // COUNTRIES — 31 destinations popular with Arab travelers
        // ============================================================
        $countries = [
            ['id' => 1,  'name_ar' => 'تركيا',                        'code' => 'TR', 'flag_emoji' => '🇹🇷'],
            ['id' => 2,  'name_ar' => 'ماليزيا',                      'code' => 'MY', 'flag_emoji' => '🇲🇾'],
            ['id' => 3,  'name_ar' => 'البوسنة والهرسك',              'code' => 'BA', 'flag_emoji' => '🇧🇦'],
            ['id' => 4,  'name_ar' => 'الإمارات العربية المتحدة',     'code' => 'AE', 'flag_emoji' => '🇦🇪'],
            ['id' => 5,  'name_ar' => 'الأردن',                       'code' => 'JO', 'flag_emoji' => '🇯🇴'],
            ['id' => 6,  'name_ar' => 'المغرب',                       'code' => 'MA', 'flag_emoji' => '🇲🇦'],
            ['id' => 7,  'name_ar' => 'إندونيسيا',                    'code' => 'ID', 'flag_emoji' => '🇮🇩'],
            ['id' => 8,  'name_ar' => 'ألبانيا',                      'code' => 'AL', 'flag_emoji' => '🇦🇱'],
            ['id' => 9,  'name_ar' => 'أذربيجان',                     'code' => 'AZ', 'flag_emoji' => '🇦🇿'],
            ['id' => 10, 'name_ar' => 'جورجيا',                       'code' => 'GE', 'flag_emoji' => '🇬🇪'],
            ['id' => 11, 'name_ar' => 'المملكة المتحدة',              'code' => 'GB', 'flag_emoji' => '🇬🇧'],
            ['id' => 12, 'name_ar' => 'فرنسا',                        'code' => 'FR', 'flag_emoji' => '🇫🇷'],
            ['id' => 13, 'name_ar' => 'ألمانيا',                      'code' => 'DE', 'flag_emoji' => '🇩🇪'],
            ['id' => 14, 'name_ar' => 'إسبانيا',                      'code' => 'ES', 'flag_emoji' => '🇪🇸'],
            ['id' => 15, 'name_ar' => 'إيطاليا',                      'code' => 'IT', 'flag_emoji' => '🇮🇹'],
            ['id' => 16, 'name_ar' => 'هولندا',                       'code' => 'NL', 'flag_emoji' => '🇳🇱'],
            ['id' => 17, 'name_ar' => 'سويسرا',                       'code' => 'CH', 'flag_emoji' => '🇨🇭'],
            ['id' => 18, 'name_ar' => 'النمسا',                       'code' => 'AT', 'flag_emoji' => '🇦🇹'],
            ['id' => 19, 'name_ar' => 'اليونان',                      'code' => 'GR', 'flag_emoji' => '🇬🇷'],
            ['id' => 20, 'name_ar' => 'البرتغال',                     'code' => 'PT', 'flag_emoji' => '🇵🇹'],
            ['id' => 21, 'name_ar' => 'تايلاند',                      'code' => 'TH', 'flag_emoji' => '🇹🇭'],
            ['id' => 22, 'name_ar' => 'سنغافورة',                     'code' => 'SG', 'flag_emoji' => '🇸🇬'],
            ['id' => 23, 'name_ar' => 'اليابان',                      'code' => 'JP', 'flag_emoji' => '🇯🇵'],
            ['id' => 24, 'name_ar' => 'كوريا الجنوبية',               'code' => 'KR', 'flag_emoji' => '🇰🇷'],
            ['id' => 25, 'name_ar' => 'المالديف',                     'code' => 'MV', 'flag_emoji' => '🇲🇻'],
            ['id' => 26, 'name_ar' => 'سريلانكا',                     'code' => 'LK', 'flag_emoji' => '🇱🇰'],
            ['id' => 27, 'name_ar' => 'كوسوفو',                       'code' => 'XK', 'flag_emoji' => '🇽🇰'],
            ['id' => 28, 'name_ar' => 'مقدونيا الشمالية',             'code' => 'MK', 'flag_emoji' => '🇲🇰'],
            ['id' => 29, 'name_ar' => 'صربيا',                        'code' => 'RS', 'flag_emoji' => '🇷🇸'],
            ['id' => 30, 'name_ar' => 'مصر',                          'code' => 'EG', 'flag_emoji' => '🇪🇬'],
            ['id' => 31, 'name_ar' => 'تونس',                         'code' => 'TN', 'flag_emoji' => '🇹🇳'],
        ];

        foreach ($countries as &$c) {
            $c['created_at'] = $now;
            $c['updated_at'] = $now;
        }

        DB::table('countries')->truncate();
        DB::table('countries')->insert($countries);

        // ============================================================
        // CITIES — 121 cities across all 31 countries
        // ============================================================
        $cities = [

            // تركيا (1)
            ['id' => 1,   'country_id' => 1,  'name_ar' => 'إسطنبول',          'latitude' =>  41.0082,  'longitude' =>  28.9784],
            ['id' => 2,   'country_id' => 1,  'name_ar' => 'أنقرة',             'latitude' =>  39.9334,  'longitude' =>  32.8597],
            ['id' => 3,   'country_id' => 1,  'name_ar' => 'أنطاليا',           'latitude' =>  36.8969,  'longitude' =>  30.7133],
            ['id' => 4,   'country_id' => 1,  'name_ar' => 'طرابزون',           'latitude' =>  41.0015,  'longitude' =>  39.7178],
            ['id' => 5,   'country_id' => 1,  'name_ar' => 'بورصة',             'latitude' =>  40.1885,  'longitude' =>  29.0610],
            ['id' => 6,   'country_id' => 1,  'name_ar' => 'إزمير',             'latitude' =>  38.4192,  'longitude' =>  27.1287],
            ['id' => 7,   'country_id' => 1,  'name_ar' => 'كابادوكيا',         'latitude' =>  38.6431,  'longitude' =>  34.8289],
            ['id' => 8,   'country_id' => 1,  'name_ar' => 'أوردو',             'latitude' =>  40.9862,  'longitude' =>  37.8797],
            ['id' => 9,   'country_id' => 1,  'name_ar' => 'غازي عنتاب',       'latitude' =>  37.0662,  'longitude' =>  37.3833],
            ['id' => 10,  'country_id' => 1,  'name_ar' => 'ألانيا',            'latitude' =>  36.5440,  'longitude' =>  31.9993],

            // ماليزيا (2)
            ['id' => 11,  'country_id' => 2,  'name_ar' => 'كوالالمبور',        'latitude' =>   3.1390,  'longitude' => 101.6869],
            ['id' => 12,  'country_id' => 2,  'name_ar' => 'بينانج',            'latitude' =>   5.4141,  'longitude' => 100.3288],
            ['id' => 13,  'country_id' => 2,  'name_ar' => 'لنكاوي',            'latitude' =>   6.3500,  'longitude' =>  99.8000],
            ['id' => 14,  'country_id' => 2,  'name_ar' => 'ملاكا',             'latitude' =>   2.1896,  'longitude' => 102.2501],
            ['id' => 15,  'country_id' => 2,  'name_ar' => 'كوتا كينابالو',     'latitude' =>   5.9804,  'longitude' => 116.0735],
            ['id' => 16,  'country_id' => 2,  'name_ar' => 'جوهور بهرو',        'latitude' =>   1.4927,  'longitude' => 103.7414],
            ['id' => 17,  'country_id' => 2,  'name_ar' => 'إيبوه',             'latitude' =>   4.5975,  'longitude' => 101.0901],
            ['id' => 18,  'country_id' => 2,  'name_ar' => 'كوتشينج',           'latitude' =>   1.5497,  'longitude' => 110.3592],

            // البوسنة والهرسك (3)
            ['id' => 19,  'country_id' => 3,  'name_ar' => 'سراييفو',           'latitude' =>  43.8563,  'longitude' =>  18.4131],
            ['id' => 20,  'country_id' => 3,  'name_ar' => 'موستار',            'latitude' =>  43.3438,  'longitude' =>  17.8078],
            ['id' => 21,  'country_id' => 3,  'name_ar' => 'بانيا لوكا',        'latitude' =>  44.7722,  'longitude' =>  17.1910],
            ['id' => 22,  'country_id' => 3,  'name_ar' => 'توزلا',             'latitude' =>  44.5384,  'longitude' =>  18.6760],
            ['id' => 23,  'country_id' => 3,  'name_ar' => 'فيسوكو',            'latitude' =>  43.9944,  'longitude' =>  18.1689],

            // الإمارات (4)
            ['id' => 24,  'country_id' => 4,  'name_ar' => 'دبي',               'latitude' =>  25.2048,  'longitude' =>  55.2708],
            ['id' => 25,  'country_id' => 4,  'name_ar' => 'أبوظبي',            'latitude' =>  24.4539,  'longitude' =>  54.3773],
            ['id' => 26,  'country_id' => 4,  'name_ar' => 'الشارقة',           'latitude' =>  25.3462,  'longitude' =>  55.4211],
            ['id' => 27,  'country_id' => 4,  'name_ar' => 'رأس الخيمة',        'latitude' =>  25.7895,  'longitude' =>  55.9432],

            // الأردن (5)
            ['id' => 28,  'country_id' => 5,  'name_ar' => 'عمّان',             'latitude' =>  31.9454,  'longitude' =>  35.9284],
            ['id' => 29,  'country_id' => 5,  'name_ar' => 'العقبة',            'latitude' =>  29.5321,  'longitude' =>  35.0063],
            ['id' => 30,  'country_id' => 5,  'name_ar' => 'البتراء',           'latitude' =>  30.3285,  'longitude' =>  35.4444],
            ['id' => 31,  'country_id' => 5,  'name_ar' => 'جرش',               'latitude' =>  32.2745,  'longitude' =>  35.8998],

            // المغرب (6)
            ['id' => 32,  'country_id' => 6,  'name_ar' => 'مراكش',             'latitude' =>  31.6295,  'longitude' =>  -7.9811],
            ['id' => 33,  'country_id' => 6,  'name_ar' => 'الدار البيضاء',     'latitude' =>  33.5731,  'longitude' =>  -7.5898],
            ['id' => 34,  'country_id' => 6,  'name_ar' => 'فاس',               'latitude' =>  34.0181,  'longitude' =>  -5.0078],
            ['id' => 35,  'country_id' => 6,  'name_ar' => 'طنجة',              'latitude' =>  35.7595,  'longitude' =>  -5.8340],
            ['id' => 36,  'country_id' => 6,  'name_ar' => 'الرباط',            'latitude' =>  33.9716,  'longitude' =>  -6.8498],
            ['id' => 37,  'country_id' => 6,  'name_ar' => 'شفشاون',            'latitude' =>  35.1688,  'longitude' =>  -5.2636],
            ['id' => 38,  'country_id' => 6,  'name_ar' => 'أكادير',            'latitude' =>  30.4278,  'longitude' =>  -9.5981],

            // إندونيسيا (7)
            ['id' => 39,  'country_id' => 7,  'name_ar' => 'بالي',              'latitude' =>  -8.3405,  'longitude' => 115.0920],
            ['id' => 40,  'country_id' => 7,  'name_ar' => 'جاكرتا',            'latitude' =>  -6.2088,  'longitude' => 106.8456],
            ['id' => 41,  'country_id' => 7,  'name_ar' => 'يوغياكارتا',        'latitude' =>  -7.7956,  'longitude' => 110.3695],
            ['id' => 42,  'country_id' => 7,  'name_ar' => 'لومبوك',            'latitude' =>  -8.6500,  'longitude' => 116.3240],
            ['id' => 43,  'country_id' => 7,  'name_ar' => 'سورابايا',          'latitude' =>  -7.2575,  'longitude' => 112.7521],

            // ألبانيا (8)
            ['id' => 44,  'country_id' => 8,  'name_ar' => 'تيرانا',            'latitude' =>  41.3275,  'longitude' =>  19.8187],
            ['id' => 45,  'country_id' => 8,  'name_ar' => 'سراندة',            'latitude' =>  39.8750,  'longitude' =>  20.0053],
            ['id' => 46,  'country_id' => 8,  'name_ar' => 'بيرات',             'latitude' =>  40.7058,  'longitude' =>  19.9522],

            // أذربيجان (9)
            ['id' => 47,  'country_id' => 9,  'name_ar' => 'باكو',              'latitude' =>  40.4093,  'longitude' =>  49.8671],
            ['id' => 48,  'country_id' => 9,  'name_ar' => 'شكي',               'latitude' =>  41.1978,  'longitude' =>  47.1706],
            ['id' => 49,  'country_id' => 9,  'name_ar' => 'غابالا',            'latitude' =>  40.9981,  'longitude' =>  47.8716],

            // جورجيا (10)
            ['id' => 50,  'country_id' => 10, 'name_ar' => 'تبليسي',            'latitude' =>  41.6938,  'longitude' =>  44.8015],
            ['id' => 51,  'country_id' => 10, 'name_ar' => 'باتومي',            'latitude' =>  41.6168,  'longitude' =>  41.6367],
            ['id' => 52,  'country_id' => 10, 'name_ar' => 'كوتايسي',           'latitude' =>  42.2679,  'longitude' =>  42.6953],

            // المملكة المتحدة (11)
            ['id' => 53,  'country_id' => 11, 'name_ar' => 'لندن',              'latitude' =>  51.5074,  'longitude' =>  -0.1278],
            ['id' => 54,  'country_id' => 11, 'name_ar' => 'برمنغهام',          'latitude' =>  52.4862,  'longitude' =>  -1.8904],
            ['id' => 55,  'country_id' => 11, 'name_ar' => 'مانشستر',           'latitude' =>  53.4808,  'longitude' =>  -2.2426],
            ['id' => 56,  'country_id' => 11, 'name_ar' => 'إدنبرة',            'latitude' =>  55.9533,  'longitude' =>  -3.1883],

            // فرنسا (12)
            ['id' => 57,  'country_id' => 12, 'name_ar' => 'باريس',             'latitude' =>  48.8566,  'longitude' =>   2.3522],
            ['id' => 58,  'country_id' => 12, 'name_ar' => 'نيس',               'latitude' =>  43.7102,  'longitude' =>   7.2620],
            ['id' => 59,  'country_id' => 12, 'name_ar' => 'ليون',              'latitude' =>  45.7640,  'longitude' =>   4.8357],
            ['id' => 60,  'country_id' => 12, 'name_ar' => 'مرسيليا',           'latitude' =>  43.2965,  'longitude' =>   5.3698],

            // ألمانيا (13)
            ['id' => 61,  'country_id' => 13, 'name_ar' => 'برلين',             'latitude' =>  52.5200,  'longitude' =>  13.4050],
            ['id' => 62,  'country_id' => 13, 'name_ar' => 'ميونخ',             'latitude' =>  48.1351,  'longitude' =>  11.5820],
            ['id' => 63,  'country_id' => 13, 'name_ar' => 'فرانكفورت',         'latitude' =>  50.1109,  'longitude' =>   8.6821],
            ['id' => 64,  'country_id' => 13, 'name_ar' => 'هامبورغ',           'latitude' =>  53.5753,  'longitude' =>  10.0153],

            // إسبانيا (14)
            ['id' => 65,  'country_id' => 14, 'name_ar' => 'مدريد',             'latitude' =>  40.4168,  'longitude' =>  -3.7038],
            ['id' => 66,  'country_id' => 14, 'name_ar' => 'برشلونة',           'latitude' =>  41.3851,  'longitude' =>   2.1734],
            ['id' => 67,  'country_id' => 14, 'name_ar' => 'إشبيلية',           'latitude' =>  37.3891,  'longitude' =>  -5.9845],
            ['id' => 68,  'country_id' => 14, 'name_ar' => 'غرناطة',            'latitude' =>  37.1773,  'longitude' =>  -3.5986],

            // إيطاليا (15)
            ['id' => 69,  'country_id' => 15, 'name_ar' => 'روما',              'latitude' =>  41.9028,  'longitude' =>  12.4964],
            ['id' => 70,  'country_id' => 15, 'name_ar' => 'ميلانو',            'latitude' =>  45.4654,  'longitude' =>   9.1859],
            ['id' => 71,  'country_id' => 15, 'name_ar' => 'البندقية',          'latitude' =>  45.4408,  'longitude' =>  12.3155],
            ['id' => 72,  'country_id' => 15, 'name_ar' => 'فلورنسا',           'latitude' =>  43.7696,  'longitude' =>  11.2558],

            // هولندا (16)
            ['id' => 73,  'country_id' => 16, 'name_ar' => 'أمستردام',          'latitude' =>  52.3676,  'longitude' =>   4.9041],
            ['id' => 74,  'country_id' => 16, 'name_ar' => 'روتردام',           'latitude' =>  51.9244,  'longitude' =>   4.4777],
            ['id' => 75,  'country_id' => 16, 'name_ar' => 'لاهاي',             'latitude' =>  52.0705,  'longitude' =>   4.3007],

            // سويسرا (17)
            ['id' => 76,  'country_id' => 17, 'name_ar' => 'زيورخ',             'latitude' =>  47.3769,  'longitude' =>   8.5417],
            ['id' => 77,  'country_id' => 17, 'name_ar' => 'جنيف',              'latitude' =>  46.2044,  'longitude' =>   6.1432],
            ['id' => 78,  'country_id' => 17, 'name_ar' => 'إنترلاكن',          'latitude' =>  46.6863,  'longitude' =>   7.8632],
            ['id' => 79,  'country_id' => 17, 'name_ar' => 'لوسيرن',            'latitude' =>  47.0502,  'longitude' =>   8.3093],

            // النمسا (18)
            ['id' => 80,  'country_id' => 18, 'name_ar' => 'فيينا',             'latitude' =>  48.2082,  'longitude' =>  16.3738],
            ['id' => 81,  'country_id' => 18, 'name_ar' => 'سالزبورغ',          'latitude' =>  47.8095,  'longitude' =>  13.0550],

            // اليونان (19)
            ['id' => 82,  'country_id' => 19, 'name_ar' => 'أثينا',             'latitude' =>  37.9838,  'longitude' =>  23.7275],
            ['id' => 83,  'country_id' => 19, 'name_ar' => 'سانتوريني',         'latitude' =>  36.3932,  'longitude' =>  25.4615],
            ['id' => 84,  'country_id' => 19, 'name_ar' => 'ميكونوس',           'latitude' =>  37.4467,  'longitude' =>  25.3289],
            ['id' => 85,  'country_id' => 19, 'name_ar' => 'كريت',              'latitude' =>  35.2401,  'longitude' =>  24.8093],
            ['id' => 86,  'country_id' => 19, 'name_ar' => 'رودس',              'latitude' =>  36.4341,  'longitude' =>  28.2176],

            // البرتغال (20)
            ['id' => 87,  'country_id' => 20, 'name_ar' => 'لشبونة',            'latitude' =>  38.7169,  'longitude' =>  -9.1395],
            ['id' => 88,  'country_id' => 20, 'name_ar' => 'بورتو',             'latitude' =>  41.1579,  'longitude' =>  -8.6291],
            ['id' => 89,  'country_id' => 20, 'name_ar' => 'الغارف',            'latitude' =>  37.0179,  'longitude' =>  -7.9307],

            // تايلاند (21)
            ['id' => 90,  'country_id' => 21, 'name_ar' => 'بانكوك',            'latitude' =>  13.7563,  'longitude' => 100.5018],
            ['id' => 91,  'country_id' => 21, 'name_ar' => 'بوكيت',             'latitude' =>   7.9519,  'longitude' =>  98.3381],
            ['id' => 92,  'country_id' => 21, 'name_ar' => 'شيانغ ماي',         'latitude' =>  18.7061,  'longitude' =>  98.9817],
            ['id' => 93,  'country_id' => 21, 'name_ar' => 'كوه ساموي',         'latitude' =>   9.5120,  'longitude' => 100.0136],

            // سنغافورة (22)
            ['id' => 94,  'country_id' => 22, 'name_ar' => 'سنغافورة',          'latitude' =>   1.3521,  'longitude' => 103.8198],

            // اليابان (23)
            ['id' => 95,  'country_id' => 23, 'name_ar' => 'طوكيو',             'latitude' =>  35.6762,  'longitude' => 139.6503],
            ['id' => 96,  'country_id' => 23, 'name_ar' => 'أوساكا',            'latitude' =>  34.6937,  'longitude' => 135.5023],
            ['id' => 97,  'country_id' => 23, 'name_ar' => 'كيوتو',             'latitude' =>  35.0116,  'longitude' => 135.7681],
            ['id' => 98,  'country_id' => 23, 'name_ar' => 'هيروشيما',          'latitude' =>  34.3853,  'longitude' => 132.4553],

            // كوريا الجنوبية (24)
            ['id' => 99,  'country_id' => 24, 'name_ar' => 'سيول',              'latitude' =>  37.5665,  'longitude' => 126.9780],
            ['id' => 100, 'country_id' => 24, 'name_ar' => 'بوسان',             'latitude' =>  35.1796,  'longitude' => 129.0756],
            ['id' => 101, 'country_id' => 24, 'name_ar' => 'جيجو',              'latitude' =>  33.4996,  'longitude' => 126.5312],

            // المالديف (25)
            ['id' => 102, 'country_id' => 25, 'name_ar' => 'ماليه',             'latitude' =>   4.1755,  'longitude' =>  73.5093],
            ['id' => 103, 'country_id' => 25, 'name_ar' => 'باا أتول',          'latitude' =>   5.1167,  'longitude' =>  72.9667],
            ['id' => 104, 'country_id' => 25, 'name_ar' => 'آري أتول',          'latitude' =>   3.8667,  'longitude' =>  72.8333],

            // سريلانكا (26)
            ['id' => 105, 'country_id' => 26, 'name_ar' => 'كولومبو',           'latitude' =>   6.9271,  'longitude' =>  79.8612],
            ['id' => 106, 'country_id' => 26, 'name_ar' => 'كاندي',             'latitude' =>   7.2906,  'longitude' =>  80.6337],

            // كوسوفو (27)
            ['id' => 107, 'country_id' => 27, 'name_ar' => 'بريشتينا',          'latitude' =>  42.6629,  'longitude' =>  21.1655],
            ['id' => 108, 'country_id' => 27, 'name_ar' => 'بيا',               'latitude' =>  42.6603,  'longitude' =>  20.2880],

            // مقدونيا الشمالية (28)
            ['id' => 109, 'country_id' => 28, 'name_ar' => 'سكوبيه',            'latitude' =>  41.9973,  'longitude' =>  21.4280],
            ['id' => 110, 'country_id' => 28, 'name_ar' => 'أوهريد',            'latitude' =>  41.1231,  'longitude' =>  20.8016],

            // صربيا (29)
            ['id' => 111, 'country_id' => 29, 'name_ar' => 'بلغراد',            'latitude' =>  44.7866,  'longitude' =>  20.4489],
            ['id' => 112, 'country_id' => 29, 'name_ar' => 'نوفي ساد',          'latitude' =>  45.2671,  'longitude' =>  19.8335],

            // مصر (30)
            ['id' => 113, 'country_id' => 30, 'name_ar' => 'القاهرة',           'latitude' =>  30.0444,  'longitude' =>  31.2357],
            ['id' => 114, 'country_id' => 30, 'name_ar' => 'الإسكندرية',        'latitude' =>  31.2001,  'longitude' =>  29.9187],
            ['id' => 115, 'country_id' => 30, 'name_ar' => 'الغردقة',           'latitude' =>  27.2578,  'longitude' =>  33.8117],
            ['id' => 116, 'country_id' => 30, 'name_ar' => 'شرم الشيخ',         'latitude' =>  27.9158,  'longitude' =>  34.3300],
            ['id' => 117, 'country_id' => 30, 'name_ar' => 'الأقصر',            'latitude' =>  25.6872,  'longitude' =>  32.6396],
            ['id' => 118, 'country_id' => 30, 'name_ar' => 'أسوان',             'latitude' =>  24.0889,  'longitude' =>  32.8998],

            // تونس (31)
            ['id' => 119, 'country_id' => 31, 'name_ar' => 'تونس العاصمة',      'latitude' =>  36.8065,  'longitude' =>  10.1815],
            ['id' => 120, 'country_id' => 31, 'name_ar' => 'سوسة',              'latitude' =>  35.8245,  'longitude' =>  10.6346],
            ['id' => 121, 'country_id' => 31, 'name_ar' => 'جربة',              'latitude' =>  33.8076,  'longitude' =>  10.8451],
        ];

        foreach ($cities as &$c) {
            $c['created_at'] = $now;
            $c['updated_at'] = $now;
        }

        DB::table('cities')->truncate();
        DB::table('cities')->insert($cities);

        Schema::enableForeignKeyConstraints();

        $this->command->info('✅ Geography seeded: 31 countries, 121 cities');
    }
}
