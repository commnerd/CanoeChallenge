<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuplicateFund extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fund_id',
    ];

    /**
     * Fund relationship
     * 
     * @return BelongsTo relationship to funds table
     */
    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class);
    }
}
