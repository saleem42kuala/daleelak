<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model
{
    /** @use HasFactory<\Database\Factories\ListingFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'area_id',
        'name_ar',
        'name_en',
        'description_ar',
        'address_ar',
        'phone',
        'latitude',
        'longitude',
        'overall_rating',
        'reviews_count',
        'promotion_rank',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'overall_rating' => 'decimal:2',
            'promotion_rank' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ListingPhoto::class);
    }

    public function listingCriteria(): HasMany
    {
        return $this->hasMany(ListingCriteria::class);
    }

    public function criteria(): BelongsToMany
    {
        return $this->belongsToMany(Criteria::class, 'listing_criteria')
            ->withPivot(['score', 'votes_count'])
            ->withTimestamps();
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function recalculateRatings(): void
    {
        $approvedReviews = $this->reviews()->where('status', 'approved');

        $reviewsCount = (clone $approvedReviews)->count();
        $overallRating = $reviewsCount > 0 ? round((clone $approvedReviews)->avg('rating'), 2) : 0;

        $this->forceFill([
            'reviews_count' => $reviewsCount,
            'overall_rating' => $overallRating,
        ])->save();

        $criteriaAverages = ReviewCriteria::query()
            ->whereIn('review_id', (clone $approvedReviews)->pluck('id'))
            ->selectRaw('criteria_id, AVG(value) * 100 as avg_score, COUNT(*) as votes_count')
            ->groupBy('criteria_id')
            ->get();

        foreach ($criteriaAverages as $row) {
            ListingCriteria::updateOrCreate(
                ['listing_id' => $this->id, 'criteria_id' => $row->criteria_id],
                ['score' => round($row->avg_score, 2), 'votes_count' => $row->votes_count],
            );
        }
    }
}
