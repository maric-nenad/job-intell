<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company'         => ['required', 'string', 'max:255'],
            'country'         => ['required', 'string', 'max:255'],
            'city'            => ['nullable', 'string', 'max:255'],
            'position'        => ['required', 'string', 'max:255'],
            'salary_min'      => ['nullable', 'integer', 'min:0'],
            'salary_max'      => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'salary_currency' => ['nullable', 'string', 'size:3'],
            'is_remote'       => ['nullable', 'boolean'],
            'status'          => ['nullable', 'in:open,closed'],
            'url'             => ['nullable', 'url', 'max:2048'],
            'posted_date'     => ['nullable', 'date'],
            'notes'           => ['nullable', 'string'],
        ];
    }
}
