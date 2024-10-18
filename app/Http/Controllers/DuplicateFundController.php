<?php

namespace App\Http\Controllers;

use App\Models\{DuplicateFund, Fund};
use Illuminate\Http\JsonResponse;

class DuplicateFundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $duplicates = DuplicateFund::paginate(self::PAGE_SIZE)->map(function($duplicate) {
            return $duplicate->fund_id;
        });

        return response()->json(Fund::whereIn('id', $duplicates)->paginate(self::PAGE_SIZE));
    }
}
