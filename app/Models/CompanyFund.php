<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyFund extends Pivot
{
    /**
     * Company relationship
     * 
     * @return BelongsTo relationship to companies table
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Fund relationship
     * 
     * @return BelongsTo relationship to funds table
     */
    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class, 'fund_id');
    }
}
