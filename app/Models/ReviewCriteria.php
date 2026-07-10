<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewCriteria extends Model
{
    protected $table = 'review_criteria';

    protected $fillable = [
        'review_id',
        'criteria_id',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'boolean',
        ];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(Criteria::class);
    }
}
