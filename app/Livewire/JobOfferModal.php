<?php

namespace App\Livewire;

use App\Models\JobOffer;
use App\Services\JobOfferService;
use Livewire\Attributes\On;
use Livewire\Component;

class JobOfferModal extends Component
{
    public bool $showModal = false;
    public ?int $offerId = null;

    public string $company = '';
    public string $country = '';
    public string $city = '';
    public string $position = '';
    public ?int $salary_min = null;
    public ?int $salary_max = null;
    public string $salary_currency = 'USD';
    public bool $is_remote = false;
    public string $status = 'open';
    public string $url = '';
    public string $posted_date = '';
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'company'         => ['required', 'string', 'max:255'],
            'country'         => ['required', 'string', 'max:255'],
            'city'            => ['nullable', 'string', 'max:255'],
            'position'        => ['required', 'string', 'max:255'],
            'salary_min'      => ['nullable', 'integer', 'min:0'],
            'salary_max'      => ['nullable', 'integer', 'min:0'],
            'salary_currency' => ['required', 'string', 'size:3'],
            'is_remote'       => ['boolean'],
            'status'          => ['required', 'in:open,closed'],
            'url'             => ['nullable', 'url', 'max:2048'],
            'posted_date'     => ['nullable', 'date'],
            'notes'           => ['nullable', 'string'],
        ];
    }

    #[On('open-job-offer-create')]
    public function create(): void
    {
        $this->reset(['offerId', 'company', 'country', 'city', 'position',
            'salary_min', 'salary_max', 'url', 'posted_date', 'notes']);
        $this->salary_currency = 'USD';
        $this->is_remote       = false;
        $this->status          = 'open';
        $this->showModal       = true;
    }

    #[On('open-job-offer-edit')]
    public function edit(int $id): void
    {
        $offer = JobOffer::findOrFail($id);

        $this->offerId         = $offer->id;
        $this->company         = $offer->company;
        $this->country         = $offer->country;
        $this->city            = $offer->city ?? '';
        $this->position        = $offer->position;
        $this->salary_min      = $offer->salary_min;
        $this->salary_max      = $offer->salary_max;
        $this->salary_currency = $offer->salary_currency;
        $this->is_remote       = $offer->is_remote;
        $this->status          = $offer->status;
        $this->url             = $offer->url ?? '';
        $this->posted_date     = $offer->posted_date?->format('Y-m-d') ?? '';
        $this->notes           = $offer->notes ?? '';
        $this->showModal       = true;
    }

    public function save(JobOfferService $service): void
    {
        $data = $this->validate();

        $data = array_map(fn($v) => $v === '' ? null : $v, $data);

        if ($this->offerId) {
            $service->update(JobOffer::findOrFail($this->offerId), $data);
        } else {
            $service->create($data, auth()->id());
        }

        $this->showModal = false;
        $this->dispatch('job-offer-saved');
        $this->dispatch('notify', message: $this->offerId ? 'Job offer updated.' : 'Job offer added.');
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.job-offer-modal');
    }
}
