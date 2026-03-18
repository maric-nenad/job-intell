<?php

use App\Http\Controllers\Api\InsightsController;
use App\Http\Controllers\Api\JobOfferController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\McpController;
use Illuminate\Support\Facades\Route;

// Issue API token (unauthenticated)
Route::post('/v1/sanctum/token', [TokenController::class, 'store']);

// Authenticated API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('v1/job-offers', JobOfferController::class);
    Route::get('v1/insights', [InsightsController::class, 'index']);

    // MCP endpoint
    Route::post('mcp', [McpController::class, 'handle']);
});
