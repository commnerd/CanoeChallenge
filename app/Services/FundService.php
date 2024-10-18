<?php

namespace App\Services;

use App\Events\DuplicateFundWarningEvent;
use App\Models\Fund;
use App\Models\FundAlias;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * General purpose service for Fund manipulations
 */
class FundService
{
    /**
     * Apply filters to a request and pass back a builder
     * 
     * @param Illuminate\Http\Request $request - Request made to endpoint
     * 
     * @return Illuminate\Database\Eloquent\Builder to continue manipulations
     */
    function applyFilters(Request $request): Builder {
        $query = Fund::query();

        if(!empty($request->name)) {
            $query = $query->where('name', 'like', "%$request->name%");
        }
        if(!empty($request->fund_manager)) {
            $query = $query->where('fund_manager_id', $request->fund_manager);
        }
        if(!empty($request->year)) {
            $query = $query->where('start_year', $request->year);
        }

        return $query;
    }

    /**
     * Test if newly added fund is a potential duplicate and fire warning event
     * on duplication found
     */
    function handleDuplicate(Fund $fund): void {
        // Is duplicate if same name and different id
        $isDuplicate = Fund::where('fund_manager_id', $fund->fund_manager_id)
            ->where('name', $fund->name)
            ->where('id', '<>', $fund->id)
            ->count() > 0;

        
        // OR is duplicate if alias has same name with different id
        $isDuplicate |= FundAlias::where('name', $fund->name)
            ->where('fund_id', '<>', $fund->id)
            ->count() > 0;
        
        DuplicateFundWarningEvent::dispatchIf($isDuplicate, $fund);
    }
}