<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Review */
class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'listing_id' => $this->listing_id,
            'rating' => $this->rating,
            'comment_ar' => $this->comment_ar,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'listing' => $this->whenLoaded('listing', fn () => [
                'id' => $this->listing->id,
                'name_ar' => $this->listing->name_ar,
            ]),
            'user' => new UserResource($this->whenLoaded('user')),
            'criteria' => $this->whenLoaded('reviewCriteria', fn () => $this->reviewCriteria->map(fn ($rc) => [
                'criteria_id' => $rc->criteria_id,
                'key' => $rc->relationLoaded('criteria') ? $rc->criteria->key : null,
                'value' => $rc->value,
            ])),
            'helpful_votes' => $this->whenCounted('votes'),
        ];
    }
}
