<?php

namespace App\Livewire;

use App\Models\Application;
use App\Models\JobOffer;
use Livewire\Component;
use Livewire\WithPagination;

class JobOfferList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $remoteFilter = '';
    public string $countryFilter = '';
    public string $sortBy = 'posted_date';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    protected $queryString = ['search', 'statusFilter', 'remoteFilter', 'countryFilter'];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingRemoteFilter(): void { $this->resetPage(); }
    public function updatingCountryFilter(): void { $this->resetPage(); }

    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'desc';
        }
    }

    public function saveOffer(int $jobOfferId): void
    {
        Application::firstOrCreate(
            ['user_id' => auth()->id(), 'job_offer_id' => $jobOfferId],
            ['status' => 'saved'],
        );
        $this->dispatch('notify', message: 'Job offer saved to tracker.');
    }

    public function deleteOffer(int $id): void
    {
        JobOffer::where('user_id', auth()->id())->findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Job offer deleted.');
    }

    public function openCreate(): void
    {
        $this->dispatch('open-job-offer-create');
    }

    public function openEdit(int $id): void
    {
        $this->dispatch('open-job-offer-edit', id: $id);
    }

    #[\Livewire\Attributes\On('job-offer-saved')]
    #[\Livewire\Attributes\On('application-saved')]
    public function refresh(): void
    {
        // triggers re-render
    }

    public function render()
    {
        $query = JobOffer::where('user_id', auth()->id());

        if ($this->search) {
            $query->search($this->search);
        }
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        if ($this->remoteFilter !== '') {
            $query->where('is_remote', $this->remoteFilter === 'remote');
        }
        if ($this->countryFilter) {
            $query->where('country', $this->countryFilter);
        }

        $query->orderBy($this->sortBy === 'posted_date' ? 'posted_date' : $this->sortBy, $this->sortDir)
              ->orderByDesc('id');

        $countries = JobOffer::where('user_id', auth()->id())->distinct()->orderBy('country')->pluck('country');

        $jobOffers = $query->paginate($this->perPage);

        $myApplications = Application::where('user_id', auth()->id())
            ->whereIn('job_offer_id', $jobOffers->pluck('id'))
            ->pluck('status', 'job_offer_id');

        return view('livewire.job-offer-list', [
            'jobOffers'      => $jobOffers,
            'countries'      => $countries,
            'myApplications' => $myApplications,
        ]);
    }
}
