<?php

namespace App\Services;

use App\Models\Area;
use App\Models\City;
use App\Models\Criteria;
use App\Models\Listing;
use App\Models\ListingCriteria;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use RuntimeException;

/**
 * Parses bulk-import files (Apify Google Maps JSON, or CSV/Excel) into a
 * normalized listing shape, and creates the listings + seeded criteria scores.
 * Used by the Filament ImportListings page.
 */
class ListingImporter
{
    /** Baseline score for every criterion unless a flag overrides it. */
    private const DEFAULT_SCORE = 70;

    /**
     * Parse an uploaded file into normalized rows.
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws RuntimeException Arabic-messaged on unreadable/unsupported input.
     */
    public function parse(string $absolutePath, string $extension): array
    {
        return match (strtolower($extension)) {
            'json' => $this->parseJson($absolutePath),
            'csv', 'xlsx' => $this->parseSpreadsheet($absolutePath),
            default => throw new RuntimeException('صيغة الملف غير مدعومة. استخدم JSON أو CSV أو Excel.'),
        };
    }

    /**
     * Create listings from normalized rows.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>  $settings  city_id, category_id, skip_duplicates, default_description
     * @return array{imported: int, skipped: int}
     */
    public function import(array $rows, array $settings, User $admin): array
    {
        $cityId = (int) $settings['city_id'];
        $categoryId = (int) $settings['category_id'];
        $skipDuplicates = (bool) ($settings['skip_duplicates'] ?? false);
        $defaultDescription = $settings['default_description'] ?? null;
        $defaultDescription = is_string($defaultDescription) && trim($defaultDescription) !== ''
            ? trim($defaultDescription)
            : null;

        $city = City::findOrFail($cityId);
        $criteria = Criteria::all(['id', 'key']);

        $imported = 0;
        $skipped = 0;

        DB::transaction(function () use (
            $rows, $cityId, $categoryId, $skipDuplicates, $defaultDescription, $city, $criteria, &$imported, &$skipped
        ) {
            // Imported listings attach to the city's default area (create one if none).
            $area = Area::firstOrCreate(
                ['city_id' => $cityId],
                ['name_ar' => 'وسط '.$city->name_ar],
            );

            foreach ($rows as $row) {
                if ($skipDuplicates && $this->isDuplicate($row['name_ar'], $cityId)) {
                    $skipped++;

                    continue;
                }

                $listing = Listing::create([
                    'category_id' => $categoryId,
                    'area_id' => $area->id,
                    'name_ar' => $row['name_ar'],
                    'name_en' => null,
                    'description_ar' => $defaultDescription,
                    'address_ar' => $row['address_ar'],
                    'phone' => $row['phone'],
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'overall_rating' => $row['overall_rating'],
                    'reviews_count' => $row['reviews_count'],
                    'is_active' => true,
                ]);

                $this->seedCriteria($listing->id, $criteria, $row['is_halal'], $row['is_family']);
                $imported++;
            }
        });

        Log::info('Listings bulk import', [
            'imported' => $imported,
            'skipped' => $skipped,
            'city_id' => $cityId,
            'category_id' => $categoryId,
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
        ]);

        return ['imported' => $imported, 'skipped' => $skipped];
    }

    private function isDuplicate(string $nameAr, int $cityId): bool
    {
        return Listing::where('name_ar', $nameAr)
            ->whereHas('area', fn ($q) => $q->where('city_id', $cityId))
            ->exists();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Criteria>  $criteria
     */
    private function seedCriteria(int $listingId, $criteria, bool $isHalal, bool $isFamily): void
    {
        $scores = [];
        foreach ($criteria as $c) {
            $scores[$c->key] = self::DEFAULT_SCORE;
        }
        if ($isHalal) {
            $scores['halal'] = 90;
            $scores['alcohol_free'] = 95;
        }
        if ($isFamily) {
            $scores['family_section'] = 90;
        }

        $now = now();
        $rows = $criteria->map(fn (Criteria $c) => [
            'listing_id' => $listingId,
            'criteria_id' => $c->id,
            'score' => $scores[$c->key] ?? self::DEFAULT_SCORE,
            'votes_count' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        ListingCriteria::insert($rows);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parseJson(string $path): array
    {
        $data = json_decode((string) file_get_contents($path), true);

        if (! is_array($data)) {
            throw new RuntimeException('تعذّر قراءة ملف JSON. تأكد من صحة التنسيق.');
        }

        // Apify exports a top-level array; tolerate a wrapper like {"items":[...]}.
        if (! array_is_list($data)) {
            $list = null;
            foreach ($data as $value) {
                if (is_array($value) && array_is_list($value)) {
                    $list = $value;
                    break;
                }
            }
            $data = $list ?? [$data];
        }

        $rows = [];
        foreach ($data as $item) {
            if (! is_array($item)) {
                continue;
            }
            $name = trim((string) ($item['title'] ?? ''));
            if ($name === '') {
                continue;
            }

            [$halal, $family] = $this->flagsFromAdditionalInfo($item['additionalInfo'] ?? []);

            $rows[] = $this->normalize([
                'name_ar' => $name,
                'address_ar' => $item['address'] ?? null,
                'phone' => $item['phoneUnformatted'] ?? ($item['phone'] ?? null),
                'latitude' => data_get($item, 'location.lat'),
                'longitude' => data_get($item, 'location.lng'),
                'overall_rating' => $item['totalScore'] ?? null,
                'reviews_count' => $item['reviewsCount'] ?? null,
                'is_halal' => $halal,
                'is_family' => $family,
            ]);
        }

        return $rows;
    }

    /**
     * Scan Apify's `additionalInfo` (nested {category: [{label: bool}]}) for
     * halal / family-friendly signals.
     *
     * @return array{0: bool, 1: bool}
     */
    private function flagsFromAdditionalInfo(mixed $info): array
    {
        $halal = false;
        $family = false;

        if (is_array($info)) {
            array_walk_recursive($info, function ($value, $key) use (&$halal, &$family) {
                if ($value !== true && $value !== 'true' && $value !== 1) {
                    return;
                }
                $label = mb_strtolower((string) $key);
                if (str_contains($label, 'halal')) {
                    $halal = true;
                }
                if (str_contains($label, 'family') || str_contains($label, 'kid') || str_contains($label, 'child')) {
                    $family = true;
                }
            });
        }

        return [$halal, $family];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parseSpreadsheet(string $path): array
    {
        try {
            $reader = ReaderFactory::createFromFile($path);
            $reader->open($path);
        } catch (\Throwable) {
            throw new RuntimeException('تعذّر قراءة ملف الجدول. تأكد من صحة الملف.');
        }

        $rows = [];
        $header = null;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = array_map(
                    fn ($c) => is_string($c) ? trim($c) : $c,
                    $row->toArray(),
                );

                if ($header === null) {
                    $header = array_map(fn ($c) => mb_strtolower(trim((string) $c)), $cells);

                    continue;
                }

                if (count(array_filter($cells, fn ($c) => $c !== null && $c !== '')) === 0) {
                    continue; // blank row
                }

                $assoc = [];
                foreach ($header as $i => $key) {
                    $assoc[$key] = $cells[$i] ?? null;
                }

                $name = trim((string) ($assoc['name_ar'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $rows[] = $this->normalize([
                    'name_ar' => $name,
                    'address_ar' => $assoc['address_ar'] ?? null,
                    'phone' => $assoc['phone'] ?? null,
                    'latitude' => $assoc['latitude'] ?? null,
                    'longitude' => $assoc['longitude'] ?? null,
                    'overall_rating' => $assoc['overall_rating'] ?? null,
                    'reviews_count' => $assoc['reviews_count'] ?? null,
                    'is_halal' => $this->toBool($assoc['is_halal'] ?? false),
                    'is_family' => $this->toBool($assoc['is_family'] ?? false),
                ]);
            }

            break; // first sheet only
        }

        $reader->close();

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $r
     * @return array<string, mixed>
     */
    private function normalize(array $r): array
    {
        return [
            'name_ar' => (string) $r['name_ar'],
            'address_ar' => $this->nullableString($r['address_ar'] ?? null),
            'phone' => $this->nullableString($r['phone'] ?? null),
            'latitude' => is_numeric($r['latitude'] ?? null) ? (float) $r['latitude'] : null,
            'longitude' => is_numeric($r['longitude'] ?? null) ? (float) $r['longitude'] : null,
            'overall_rating' => is_numeric($r['overall_rating'] ?? null)
                ? round(min(5, max(0, (float) $r['overall_rating'])), 2)
                : 0,
            'reviews_count' => is_numeric($r['reviews_count'] ?? null) ? max(0, (int) $r['reviews_count']) : 0,
            'is_halal' => (bool) ($r['is_halal'] ?? false),
            'is_family' => (bool) ($r['is_family'] ?? false),
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(mb_strtolower(trim((string) $value)), ['1', 'true', 'yes', 'y', 'نعم', 'صحيح'], true);
    }
}
