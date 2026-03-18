<?php

namespace App\Services;

use App\Models\JobOffer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class JobOfferService
{
    public function create(array $data, ?int $userId = null): JobOffer
    {
        return JobOffer::create(array_merge($data, ['user_id' => $userId]));
    }

    public function update(JobOffer $jobOffer, array $data): JobOffer
    {
        $jobOffer->update($data);
        return $jobOffer->fresh();
    }

    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = JobOffer::query()->latest('posted_date')->latest('id');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }
        if (isset($filters['is_remote'])) {
            $query->where('is_remote', $filters['is_remote']);
        }
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->paginate($perPage);
    }

    public function insights(int $userId): array
    {
        $base = JobOffer::where('user_id', $userId);

        $total  = (clone $base)->count();
        $open   = (clone $base)->where('status', 'open')->count();
        $closed = (clone $base)->where('status', 'closed')->count();
        $remote = (clone $base)->where('is_remote', true)->count();
        $onsite = (clone $base)->where('is_remote', false)->count();

        $topCountries = (clone $base)
            ->select('country', DB::raw('count(*) as total'))
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'country')
            ->toArray();

        $topCities = (clone $base)
            ->select('city', DB::raw('count(*) as total'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'city')
            ->toArray();

        $topCompanies = (clone $base)
            ->select('company', DB::raw('count(*) as total'))
            ->groupBy('company')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'company')
            ->toArray();

        $monthlyTrend = (clone $base)
            ->select(
                DB::raw("TO_CHAR(posted_date, 'Mon YYYY') as month"),
                DB::raw("DATE_TRUNC('month', posted_date) as month_start"),
                DB::raw('count(*) as total')
            )
            ->whereNotNull('posted_date')
            ->where('posted_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month', 'month_start')
            ->orderBy('month_start')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        return compact(
            'total', 'open', 'closed', 'remote', 'onsite',
            'topCountries', 'topCities', 'topCompanies', 'monthlyTrend'
        );
    }
}
