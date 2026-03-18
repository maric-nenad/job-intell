<?php

namespace App\Livewire;

use App\Models\Application;
use App\Models\Cv;
use App\Models\JobOffer;
use Livewire\Component;

class ApplicationModal extends Component
{
    public bool $showModal = false;
    public ?int $applicationId = null;
    public ?int $jobOfferId = null;

    public string $jobCompany = '';
    public string $jobPosition = '';
    public string $jobLocation = '';

    public string $status = 'saved';
    public ?int $cv_id = null;
    public string $applied_date = '';
    public string $next_follow_up = '';
    public string $screening_time = '';
    public string $screening_contact = '';
    public string $interview_time = '';
    public string $interview_contact = '';
    public array $interview_interviewers = [];
    public string $new_interviewer = '';
    public string $offer_salary = '';
    public string $offer_benefits = '';
    public string $rejected_at = '';
    public string $withdrawn_at = '';
    public string $notes = '';

    // Statuses that require a CV selection
    public const CV_REQUIRED_STATUSES = ['applied', 'screening', 'interview', 'offer'];

    protected function rules(): array
    {
        return [
            'status'         => ['required', 'in:saved,preparation,applied,screening,interview,offer,rejected,withdrawn'],
            'cv_id'          => [in_array($this->status, self::CV_REQUIRED_STATUSES) ? 'required' : 'nullable', 'nullable', 'exists:cvs,id'],
            'applied_date'      => ['nullable', 'date'],
            'next_follow_up'    => ['nullable', 'date'],
            'screening_time'         => ['nullable', 'date_format:Y-m-d\TH:i'],
            'screening_contact'      => ['nullable', 'string', 'max:255'],
            'interview_time'         => ['nullable', 'date_format:Y-m-d\TH:i'],
            'interview_contact'      => ['nullable', 'string', 'max:255'],
            'interview_interviewers' => ['nullable', 'array'],
            'offer_salary'           => ['nullable', 'string', 'max:255'],
            'offer_benefits'         => ['nullable', 'string'],
            'rejected_at'            => ['nullable', 'date'],
            'withdrawn_at'           => ['nullable', 'date'],
            'notes'                  => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'cv_id.required' => 'Please select a CV version for this application stage.',
        ];
    }

    public function openForOffer(int $jobOfferId): void
    {
        $offer    = JobOffer::findOrFail($jobOfferId);
        $existing = Application::where('user_id', auth()->id())
            ->where('job_offer_id', $jobOfferId)
            ->first();

        $this->jobOfferId  = $jobOfferId;
        $this->jobCompany  = $offer->company;
        $this->jobPosition = $offer->position;
        $this->jobLocation = collect([$offer->city, $offer->country])->filter()->implode(', ');

        if ($existing) {
            $this->applicationId  = $existing->id;
            $this->status         = $existing->status;
            $this->cv_id          = $existing->cv_id;
            $this->applied_date      = $existing->applied_date?->format('Y-m-d') ?? '';
            $this->next_follow_up    = $existing->next_follow_up?->format('Y-m-d') ?? '';
            $this->screening_time         = $existing->screening_time?->format('Y-m-d\TH:i') ?? '';
            $this->screening_contact      = $existing->screening_contact ?? '';
            $this->interview_time         = $existing->interview_time?->format('Y-m-d\TH:i') ?? '';
            $this->interview_contact      = $existing->interview_contact ?? '';
            $this->interview_interviewers = $existing->interview_interviewers ?? [];
            $this->offer_salary           = $existing->offer_salary ?? '';
            $this->offer_benefits         = $existing->offer_benefits ?? '';
            $this->rejected_at            = $existing->rejected_at?->format('Y-m-d') ?? '';
            $this->withdrawn_at           = $existing->withdrawn_at?->format('Y-m-d') ?? '';
            $this->notes                  = $existing->notes ?? '';
        } else {
            $this->reset(['applicationId', 'status', 'cv_id', 'applied_date', 'next_follow_up', 'screening_time', 'screening_contact', 'interview_time', 'interview_contact', 'interview_interviewers', 'offer_salary', 'offer_benefits', 'rejected_at', 'withdrawn_at', 'notes']);
            $this->status = 'saved';
        }

        $this->showModal = true;
    }

    public function addInterviewer(): void
    {
        $name = trim($this->new_interviewer);
        if ($name !== '' && !in_array($name, $this->interview_interviewers)) {
            $this->interview_interviewers[] = $name;
        }
        $this->new_interviewer = '';
    }

    public function removeInterviewer(int $index): void
    {
        array_splice($this->interview_interviewers, $index, 1);
    }

    public function save(): void
    {
        $data = $this->validate();
        unset($data['new_interviewer']);
        $data = array_map(fn($v) => $v === '' ? null : $v, $data);
        if (isset($data['interview_interviewers']) && $data['interview_interviewers'] === []) {
            $data['interview_interviewers'] = null;
        }

        $existing = Application::where('user_id', auth()->id())
            ->where('job_offer_id', $this->jobOfferId)
            ->first();

        if (!$existing || $existing->status !== $data['status']) {
            $data['status_changed_at'] = now();
        }

        Application::updateOrCreate(
            ['user_id' => auth()->id(), 'job_offer_id' => $this->jobOfferId],
            $data
        );

        $this->showModal = false;
        $this->dispatch('application-saved');
        $this->dispatch('notify', message: 'Application saved.');
    }

    public function delete(): void
    {
        Application::where('user_id', auth()->id())
            ->where('job_offer_id', $this->jobOfferId)
            ->delete();

        $this->showModal = false;
        $this->dispatch('application-saved');
        $this->dispatch('notify', message: 'Application removed.');
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.application-modal', [
            'statuses'          => Application::$statuses,
            'cvs'               => Cv::where('user_id', auth()->id())->latest()->get(),
            'cvRequiredStatuses' => self::CV_REQUIRED_STATUSES,
        ]);
    }
}
