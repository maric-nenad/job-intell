<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JobOfferService;
use Illuminate\Http\JsonResponse;

class InsightsController extends Controller
{
    public function __construct(private JobOfferService $service) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->insights());
    }
}
