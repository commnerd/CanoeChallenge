<?php

namespace App\Services;

use App\Events\DuplicateFundWarningEvent;
use App\Models\Fund;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FundService
{
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

    function handleDuplicate(Fund $fund): void {
        $isDuplicate = Fund::where('fund_manager_id', $fund->fund_manager_id)
            ->where('name', $fund->name)
            ->where('id', '<>', $fund->id)
            ->count() > 0;

        $isDuplicate |= in_array($fund->name, $fund->aliases->pluck('name')->toArray());
        
        DuplicateFundWarningEvent::dispatchIf($isDuplicate, $fund);
    }
}