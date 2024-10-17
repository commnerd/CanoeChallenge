<?php

namespace App\Services;

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

}