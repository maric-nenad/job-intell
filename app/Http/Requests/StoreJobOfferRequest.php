<?php

namespace App\Http\Requests;

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
            'company'               => ['required', 'string', 'max:255'],
            'country'               => ['required', 'string', 'max:255'],
            'city'                  => ['nullable', 'string', 'max:255'],
            'position'              => ['required', 'string', 'max:255'],
            'salary_min'            => ['nullable', 'integer', 'min:0'],
            'salary_max'            => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'salary_currency'       => ['nullable', 'string', 'size:3'],
            'is_remote'             => ['nullable', 'boolean'],
            'status'                => ['nullable', 'in:open,closed'],
            'url'                   => ['nullable', 'url', 'max:2048'],
            'posted_date'           => ['nullable', 'date'],
            'summary'               => ['nullable', 'string'],
            'skills'                => ['nullable', 'array'],
            'skills.*'              => ['string', 'max:100'],
            'notes'                 => ['nullable', 'string'],
            'company_rating'        => ['nullable', 'numeric', 'min:1', 'max:5'],
            'company_rating_source' => ['nullable', 'string', 'max:255'],
            'company_valuation'     => ['nullable', 'string', 'max:255'],
            'company_employees'     => ['nullable', 'string', 'max:255'],
            'company_owners'        => ['nullable', 'string'],
            'ats_probability'       => ['nullable', 'in:low,medium,high'],
        ];
    }
}
