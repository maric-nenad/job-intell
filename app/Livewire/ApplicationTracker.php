<?php

namespace App\Livewire;

use App\Models\Application;
use Livewire\Component;

class ApplicationTracker extends Component
{
    public string $search = '';
    public string $statusFilter = '';

    #[\Livewire\Attributes\On('application-saved')]
    public function refresh(): void {}

    public function updateStatus(int $applicationId, string $status): void
    {
        Application::where('id', $applicationId)
            ->where('user_id', auth()->id())
            ->update(['status' => $status, 'status_changed_at' => now()]);

        $this->dispatch('notify', message: 'Status updated.');
    }

    public function render()
    {
        $query = Application::with('jobOffer', 'cv')
            ->where('user_id', auth()->id())
            ->when($this->search, fn($q) => $q->whereHas('jobOffer', fn($j) =>
                $j->where('company', 'like', "%{$this->search}%")
                  ->orWhere('position', 'like', "%{$this->search}%")
            ))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest();

        $applications = $query->get();

        $pipeline = collect(Application::$statuses)->mapWithKeys(fn($meta, $key) => [
            $key => [
                'meta'         => $meta,
                'applications' => $applications->where('status', $key)->values(),
            ],
        ]);

        $counts = $applications->groupBy('status')->map->count();

        return view('livewire.application-tracker', compact('pipeline', 'counts'));
    }
}
