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
    public string $summary = '';
    public array $skills = [];
    public string $new_skill = '';
    public string $posted_date = '';
    public string $notes = '';
    public ?float $company_rating = null;
    public string $company_rating_source = '';
    public string $company_valuation = '';
    public string $company_employees = '';
    public string $company_owners = '';
    public string $ats_probability = '';

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
            'summary'         => ['nullable', 'string'],
            'skills'          => ['nullable', 'array'],
            'posted_date'           => ['nullable', 'date'],
            'notes'                 => ['nullable', 'string'],
            'company_rating'        => ['nullable', 'numeric', 'min:1', 'max:5'],
            'company_rating_source' => ['nullable', 'string', 'max:255'],
            'company_valuation'     => ['nullable', 'string', 'max:255'],
            'company_employees'     => ['nullable', 'string', 'max:255'],
            'company_owners'        => ['nullable', 'string'],
            'ats_probability'       => ['nullable', 'in:low,medium,high'],
        ];
    }

    #[On('open-job-offer-create')]
    public function create(): void
    {
        $this->reset(['offerId', 'company', 'country', 'city', 'position',
            'salary_min', 'salary_max', 'url', 'summary', 'skills', 'new_skill', 'posted_date', 'notes',
            'company_rating', 'company_rating_source', 'company_valuation', 'company_employees', 'company_owners',
            'ats_probability']);
        $this->salary_currency = 'USD';
        $this->is_remote       = false;
        $this->status          = 'open';
        $this->showModal       = true;
    }

    #[On('open-job-offer-edit')]
    public function edit(int $id): void
    {
        $offer = JobOffer::where('user_id', auth()->id())->findOrFail($id);

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
        $this->summary         = $offer->summary ?? '';
        $this->skills          = $offer->skills ?? [];
        $this->new_skill       = '';
        $this->posted_date            = $offer->posted_date?->format('Y-m-d') ?? '';
        $this->notes                  = $offer->notes ?? '';
        $this->company_rating         = $offer->company_rating;
        $this->company_rating_source  = $offer->company_rating_source ?? '';
        $this->company_valuation      = $offer->company_valuation ?? '';
        $this->company_employees      = $offer->company_employees ?? '';
        $this->company_owners         = $offer->company_owners ?? '';
        $this->ats_probability        = $offer->ats_probability ?? '';
        $this->showModal              = true;
    }

    public function addSkill(): void
    {
        $skill = trim($this->new_skill);
        if ($skill !== '' && !in_array($skill, $this->skills)) {
            $this->skills[] = $skill;
        }
        $this->new_skill = '';
    }

    public function removeSkill(int $index): void
    {
        array_splice($this->skills, $index, 1);
    }

    public function save(JobOfferService $service): void
    {
        $data = $this->validate();
        unset($data['new_skill']);

        $data = array_map(fn($v) => $v === '' ? null : $v, $data);
        if (isset($data['skills']) && $data['skills'] === []) {
            $data['skills'] = null;
        }

        if ($this->offerId) {
            $service->update(JobOffer::where('user_id', auth()->id())->findOrFail($this->offerId), $data);
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
