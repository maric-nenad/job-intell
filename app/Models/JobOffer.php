<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company',
        'country',
        'city',
        'position',
        'salary_min',
        'salary_max',
        'salary_currency',
        'is_remote',
        'status',
        'url',
        'summary',
        'skills',
        'posted_date',
        'notes',
        'company_rating',
        'company_rating_source',
        'company_valuation',
        'company_employees',
        'company_owners',
        'ats_probability',
    ];

    public static array $atsProbabilityLevels = [
        'low'    => ['label' => 'Low',    'color' => 'green',  'hint' => 'Likely human review'],
        'medium' => ['label' => 'Medium', 'color' => 'yellow', 'hint' => 'Possible ATS screening'],
        'high'   => ['label' => 'High',   'color' => 'red',    'hint' => 'Likely automated ATS/AI screening'],
    ];

    protected function casts(): array
    {
        return [
            'is_remote'      => 'boolean',
            'salary_min'     => 'integer',
            'salary_max'     => 'integer',
            'posted_date'    => 'date',
            'skills'         => 'array',
            'company_rating' => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function applicationFor(int $userId): ?Application
    {
        return $this->applications()->where('user_id', $userId)->first();
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function scopeRemote(Builder $query): Builder
    {
        return $query->where('is_remote', true);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('company', 'like', "%{$search}%")
              ->orWhere('position', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('country', 'like', "%{$search}%");
        });
    }
}
