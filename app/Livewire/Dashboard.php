<?php

namespace App\Livewire;

use App\Services\JobOfferService;
use Livewire\Component;

class Dashboard extends Component
{
    public int $total = 0;
    public int $open = 0;
    public int $closed = 0;
    public int $remote = 0;
    public int $onsite = 0;
    public array $topCountries = [];
    public array $topCities = [];
    public array $topCompanies = [];
    public array $monthlyTrend = [];

    public function mount(JobOfferService $service): void
    {
        $data = $service->insights();

        $this->total        = $data['total'];
        $this->open         = $data['open'];
        $this->closed       = $data['closed'];
        $this->remote       = $data['remote'];
        $this->onsite       = $data['onsite'];
        $this->topCountries = $data['topCountries'];
        $this->topCities    = $data['topCities'];
        $this->topCompanies = $data['topCompanies'];
        $this->monthlyTrend = $data['monthlyTrend'];
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
