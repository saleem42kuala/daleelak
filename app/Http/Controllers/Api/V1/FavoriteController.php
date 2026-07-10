<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreFavoriteRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FavoriteController extends Controller
{
    /**
     * The authenticated user's favorite listings.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $listings = $request->user()
            ->favoriteListings()
            ->with(['category', 'area.city.country', 'photos'])
            ->latest('favorites.created_at')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return ListingResource::collection($listings);
    }

    /**
     * Add a listing to favorites (idempotent).
     */
    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        $request->user()->favoriteListings()->syncWithoutDetaching([
            $request->integer('listing_id'),
        ]);

        return response()->json(['message' => 'تمت الإضافة إلى المفضلة.'], 201);
    }

    /**
     * Remove a listing from favorites.
     */
    public function destroy(Request $request, Listing $listing): JsonResponse
    {
        $request->user()->favoriteListings()->detach($listing->id);

        return response()->json(['message' => 'تمت الإزالة من المفضلة.']);
    }
}
