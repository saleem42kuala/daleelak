<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin \App\Models\Listing */
class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = $this->relationLoaded('photos')
            ? $this->photos->firstWhere('is_cover', true) ?? $this->photos->first()
            : null;

        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'description_ar' => $this->description_ar,
            'address_ar' => $this->address_ar,
            'phone' => $this->phone,
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'overall_rating' => (float) $this->overall_rating,
            'reviews_count' => $this->reviews_count,
            'is_active' => $this->is_active,
            'cover_photo' => $cover ? Storage::disk('public')->url($cover->path) : null,
            'distance_km' => $this->when(isset($this->distance_km), fn () => round((float) $this->distance_km, 2)),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'area' => new AreaResource($this->whenLoaded('area')),
            'photos' => ListingPhotoResource::collection($this->whenLoaded('photos')),
            'criteria_scores' => ListingCriteriaResource::collection($this->whenLoaded('listingCriteria')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
