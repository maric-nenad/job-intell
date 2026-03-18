<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'job_offer_id',
        'cv_id',
        'status',
        'status_changed_at',
        'applied_date',
        'next_follow_up',
        'screening_time',
        'screening_contact',
        'interview_time',
        'interview_contact',
        'interview_interviewers',
        'offer_salary',
        'offer_benefits',
        'rejected_at',
        'withdrawn_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'applied_date'   => 'date',
            'next_follow_up' => 'date',
            'screening_time'         => 'datetime',
            'interview_time'         => 'datetime',
            'interview_interviewers' => 'array',
            'rejected_at'            => 'date',
            'withdrawn_at'           => 'date',
            'status_changed_at'      => 'datetime',
        ];
    }

    public static array $statuses = [
        'saved'       => ['label' => 'Saved',        'color' => 'gray'],
        'preparation' => ['label' => 'Preparation',  'color' => 'teal'],
        'applied'     => ['label' => 'Applied',      'color' => 'blue'],
        'screening' => ['label' => 'Screening',  'color' => 'yellow'],
        'interview' => ['label' => 'Interview',  'color' => 'purple'],
        'offer'     => ['label' => 'Offer',      'color' => 'green'],
        'rejected'  => ['label' => 'Rejected',   'color' => 'red'],
        'withdrawn' => ['label' => 'Withdrawn',  'color' => 'orange'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
