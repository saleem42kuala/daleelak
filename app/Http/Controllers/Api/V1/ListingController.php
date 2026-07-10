<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ListingIndexRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ListingController extends Controller
{
    /**
     * A listing_criteria score at/above this percentage counts as the listing
     * "offering" that criterion (i.e. most reviewers confirmed it).
     */
    private const CRITERIA_MATCH_THRESHOLD = 50;

    public function index(ListingIndexRequest $request): ResourceCollection
    {
        $query = Listing::query()
            ->where('is_active', true)
            ->with(['category', 'area.city.country', 'photos', 'listingCriteria.criteria']);

        // --- Filters ---
        $query->when($request->integer('category_id'), fn ($q, $id) => $q->where('category_id', $id));

        $query->when($request->integer('city_id'), fn ($q, $id) => $q->whereHas('area', fn ($a) => $a->where('city_id', $id)));

        $query->when($request->integer('country_id'), fn ($q, $id) => $q->whereHas('area.city', fn ($c) => $c->where('country_id', $id)));

        $query->when($request->filled('search'), function ($q) use ($request) {
            $term = '%'.$request->string('search').'%';
            $q->where(fn ($w) => $w->where('name_ar', 'like', $term)->orWhere('name_en', 'like', $term));
        });

        // Each requested criterion slug must be offered by the listing (AND).
        foreach ((array) $request->input('criteria', []) as $key) {
            $query->whereHas('listingCriteria', function ($q) use ($key) {
                $q->where('score', '>=', self::CRITERIA_MATCH_THRESHOLD)
                    ->whereHas('criteria', fn ($c) => $c->where('key', $key));
            });
        }

        // --- Sorting ---
        $this->applySort($query, $request);

        $listings = $query->paginate($request->integer('per_page', 15))->withQueryString();

        return ListingResource::collection($listings);
    }

    public function show(Listing $listing): ListingResource
    {
        abort_unless($listing->is_active, 404);

        $listing->load([
            'category',
            'area.city.country',
            'photos',
            'listingCriteria.criteria',
            'reviews' => fn ($q) => $q->where('status', 'approved')->latest()->limit(20),
            'reviews.user',
            'reviews.reviewCriteria.criteria',
        ])->loadCount(['reviews as helpful_reviews' => fn ($q) => $q->where('status', 'approved')]);

        return new ListingResource($listing);
    }

    private function applySort($query, ListingIndexRequest $request): void
    {
        switch ($request->input('sort')) {
            case 'nearest':
                // Haversine distance (km) from the supplied point.
                $lat = (float) $request->input('lat');
                $lng = (float) $request->input('lng');

                $query->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->select('listings.*')
                    ->selectRaw(
                        '( 6371 * acos( cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)) ) ) AS distance_km',
                        [$lat, $lng, $lat]
                    )
                    ->orderBy('distance_km');
                break;

            case 'most_reviewed':
                $query->orderByDesc('reviews_count')->orderByDesc('overall_rating');
                break;

            case 'top_rated':
            default:
                $query->orderByDesc('overall_rating')->orderByDesc('reviews_count');
                break;
        }
    }
}
