<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Cv extends Model
{
    protected $table = 'cvs';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'file_path',
        'file_name',
        'file_size',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $kb = $this->file_size / 1024;
        return $kb >= 1024
            ? number_format($kb / 1024, 1) . ' MB'
            : number_format($kb, 0) . ' KB';
    }

    public function deleteFile(): void
    {
        Storage::disk('private')->delete($this->file_path);
    }
}
