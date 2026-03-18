<?php

namespace App\Livewire;

use App\Models\Application;
use Livewire\Component;

class ApplicationAnalytics extends Component
{
    public string $search = '';

    public function render()
    {
        $applications = Application::with('jobOffer', 'cv')
            ->where('user_id', auth()->id())
            ->when($this->search, fn($q) => $q->whereHas('jobOffer', fn($j) =>
                $j->where('company', 'like', "%{$this->search}%")
                  ->orWhere('position', 'like', "%{$this->search}%")
            ))
            ->latest()
            ->get();

        $counts = Application::where('user_id', auth()->id())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('livewire.application-analytics', compact('applications', 'counts'));
    }
}
