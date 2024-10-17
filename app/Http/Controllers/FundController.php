<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFundRequest;
use App\Http\Requests\UpdateFundRequest;
use App\Models\Fund;
use Illuminate\Http\JsonResponse;

class FundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Fund::paginate(self::PAGE_SIZE));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFundRequest $request): JsonResponse
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Fund $fund): JsonResponse
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFundRequest $request, Fund $fund): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fund $fund): JsonResponse
    {
        //
    }
}
