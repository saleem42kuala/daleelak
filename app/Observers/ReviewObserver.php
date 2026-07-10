<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        $review->listing->recalculateRatings();
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        if ($review->wasChanged(['status', 'rating'])) {
            $review->listing->recalculateRatings();
        }
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        $review->listing->recalculateRatings();
    }

    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        $review->listing->recalculateRatings();
    }

    /**
     * Handle the Review "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        $review->listing->recalculateRatings();
    }
}
