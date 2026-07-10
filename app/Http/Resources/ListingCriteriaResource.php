<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ListingCriteria */
class ListingCriteriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'criteria_id' => $this->criteria_id,
            'key' => $this->whenLoaded('criteria', fn () => $this->criteria->key),
            'name_ar' => $this->whenLoaded('criteria', fn () => $this->criteria->name_ar),
            'score' => (float) $this->score,
            'votes_count' => $this->votes_count,
        ];
    }
}
