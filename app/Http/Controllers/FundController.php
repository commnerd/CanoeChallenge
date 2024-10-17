<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFundRequest;
use App\Http\Requests\UpdateFundRequest;
use App\Models\Fund;
use Illuminate\Http\{JsonResponse, Request};

class FundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $fundService = app()->make(\App\Services\FundService::class);

        return response()->json($fundService->applyFilters($request)->paginate(self::PAGE_SIZE));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFundRequest $request): JsonResponse
    {
        $fund = Fund::create($request->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $fund->toArray(),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Fund $fund): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $fund->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFundRequest $request, Fund $fund): JsonResponse
    {
        $fund->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $fund->toArray(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fund $fund): JsonResponse
    {
        $fund->delete();

        return response()->json(['status' => 'success']);
    }
}
