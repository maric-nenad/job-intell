<?php

namespace App\Livewire;

use App\Models\Application;
use App\Models\JobOffer;
use Livewire\Component;

class JobOfferDetail extends Component
{
    public bool $show = false;
    public ?int $jobOfferId = null;

    public function open(int $jobOfferId): void
    {
        $this->jobOfferId = $jobOfferId;
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->jobOfferId = null;
    }

    public function render()
    {
        $offer       = $this->jobOfferId ? JobOffer::where('user_id', auth()->id())->find($this->jobOfferId) : null;
        $application = $offer
            ? Application::with('cv')
                ->where('user_id', auth()->id())
                ->where('job_offer_id', $this->jobOfferId)
                ->first()
            : null;

        return view('livewire.job-offer-detail', compact('offer', 'application'));
    }
}
