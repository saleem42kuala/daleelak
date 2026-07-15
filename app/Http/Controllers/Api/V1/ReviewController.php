<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Approved reviews for a listing.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate(['listing_id' => ['required', 'integer', 'exists:listings,id']]);

        $reviews = Review::query()
            ->where('listing_id', $request->integer('listing_id'))
            ->where('status', 'approved')
            ->with(['user', 'reviewCriteria.criteria'])
            ->withCount('votes')
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return ReviewResource::collection($reviews);
    }

    /**
     * The authenticated user's own reviews (all statuses), with the listing
     * they belong to, for the "تقييماتي" section on the account screen.
     */
    public function mine(Request $request): AnonymousResourceCollection
    {
        $reviews = $request->user()
            ->reviews()
            ->with(['listing', 'reviewCriteria.criteria'])
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return ReviewResource::collection($reviews);
    }

    /**
     * Submit a review. Persisted as `pending`; the ReviewObserver recalculates
     * the listing's cached aggregates once (and if) it is approved.
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = DB::transaction(function () use ($request) {
            $review = Review::create([
                'listing_id' => $request->integer('listing_id'),
                'user_id' => $request->user()->id,
                'rating' => $request->integer('rating'),
                'comment_ar' => $request->string('comment_ar'),
                'status' => 'pending',
            ]);

            foreach ((array) $request->input('criteria', []) as $item) {
                $review->reviewCriteria()->create([
                    'criteria_id' => $item['criteria_id'],
                    'value' => (bool) $item['value'],
                ]);
            }

            return $review;
        });

        return (new ReviewResource($review->load('reviewCriteria.criteria')))
            ->additional(['message' => 'تم استلام مراجعتك وهي قيد المراجعة.'])
            ->response()
            ->setStatusCode(201);
    }
}
