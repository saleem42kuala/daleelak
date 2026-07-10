<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criteria extends Model
{
    /** @use HasFactory<\Database\Factories\CriteriaFactory> */
    use HasFactory;

    protected $table = 'criteria';

    protected $fillable = [
        'key',
        'name_ar',
        'name_en',
        'icon',
        'sort_order',
    ];

    public function listingCriteria(): HasMany
    {
        return $this->hasMany(ListingCriteria::class);
    }

    public function reviewCriteria(): HasMany
    {
        return $this->hasMany(ReviewCriteria::class);
    }
}
