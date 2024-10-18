<?php

namespace App\Http\Controllers;

use App\Models\DuplicateFund;
use Illuminate\Http\JsonResponse;

class DuplicateFundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(DuplicateFund::paginate(self::PAGE_SIZE));
    }
}
