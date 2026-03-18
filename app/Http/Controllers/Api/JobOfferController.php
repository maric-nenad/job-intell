<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobOfferRequest;
use App\Http\Requests\UpdateJobOfferRequest;
use App\Models\JobOffer;
use App\Services\JobOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{
    public function __construct(private JobOfferService $service) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'country', 'is_remote', 'search']);
        if (isset($filters['is_remote'])) {
            $filters['is_remote'] = filter_var($filters['is_remote'], FILTER_VALIDATE_BOOLEAN);
        }
        $perPage = min((int) $request->get('per_page', 20), 100);

        $paginated = $this->service->list($filters, $perPage);

        return response()->json($paginated);
    }

    public function store(StoreJobOfferRequest $request): JsonResponse
    {
        $offer = $this->service->create($request->validated(), $request->user()->id);

        return response()->json($offer, 201);
    }

    public function show(JobOffer $jobOffer): JsonResponse
    {
        return response()->json($jobOffer);
    }

    public function update(UpdateJobOfferRequest $request, JobOffer $jobOffer): JsonResponse
    {
        $offer = $this->service->update($jobOffer, $request->validated());

        return response()->json($offer);
    }

    public function destroy(JobOffer $jobOffer): JsonResponse
    {
        $jobOffer->delete();

        return response()->json(null, 204);
    }
}
