<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundAlias extends Model
{
    use HasFactory;

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
