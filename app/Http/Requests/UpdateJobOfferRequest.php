<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJobOfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company'         => ['sometimes', 'required', 'string', 'max:255'],
            'country'         => ['sometimes', 'required', 'string', 'max:255'],
            'city'            => ['nullable', 'string', 'max:255'],
            'position'        => ['sometimes', 'required', 'string', 'max:255'],
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
