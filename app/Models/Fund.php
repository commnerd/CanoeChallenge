<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fund extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_year',
        'fund_manager_id'
    ];

    /**
     * Aliases relationship
     * 
     * @return HasMany relationship to fund_aliases table
     */
    public function aliases(): HasMany
    {
        return $this->hasMany(FundAlias::class);
    }

    /**
     * Portfolio of invested companies
     * 
     * @return HasManyThrough
     */
    public function portfolio(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_fund', 'fund_id', 'company_id');
    }
}
