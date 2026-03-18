<?php

namespace App\Livewire;

use App\Models\Application;
use Livewire\Component;

class ApplicationDetail extends Component
{
    public bool $show = false;
    public ?int $applicationId = null;

    public function load(int $applicationId): void
    {
        $this->applicationId = $applicationId;
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->applicationId = null;
    }

    public function render()
    {
        $application = $this->applicationId
            ? Application::with('jobOffer', 'cv')
                ->where('user_id', auth()->id())
                ->find($this->applicationId)
            : null;

        return view('livewire.application-detail', compact('application'));
    }
}
