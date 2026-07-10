<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReviewVoteRequest;
use App\Models\ReviewVote;
use Illuminate\Http\JsonResponse;

class ReviewVoteController extends Controller
{
    /**
     * Cast (or update) a helpful/not-helpful vote on a review.
     */
    public function store(StoreReviewVoteRequest $request): JsonResponse
    {
        ReviewVote::updateOrCreate(
            [
                'review_id' => $request->integer('review_id'),
                'user_id' => $request->user()->id,
            ],
            ['is_helpful' => $request->boolean('is_helpful')],
        );

        $helpfulCount = ReviewVote::where('review_id', $request->integer('review_id'))
            ->where('is_helpful', true)
            ->count();

        return response()->json([
            'message' => 'تم تسجيل تصويتك.',
            'helpful_votes' => $helpfulCount,
        ]);
    }
}
