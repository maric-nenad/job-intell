<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Services\JobOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class McpController extends Controller
{
    public function __construct(private JobOfferService $service) {}

    public function handle(Request $request): JsonResponse
    {
        $body = $request->json()->all();

        if (($body['jsonrpc'] ?? '') !== '2.0' || empty($body['method'])) {
            return $this->error(null, -32600, 'Invalid Request');
        }

        $id     = $body['id'] ?? null;
        $method = $body['method'];
        $params = $body['params'] ?? [];

        return match ($method) {
            'initialize'  => $this->initialize($id),
            'tools/list'  => $this->toolsList($id),
            'tools/call'  => $this->toolsCall($id, $params, $request->user()),
            default       => $this->error($id, -32601, "Method not found: {$method}"),
        };
    }

    private function initialize(mixed $id): JsonResponse
    {
        return $this->result($id, [
            'protocolVersion' => '2024-11-05',
            'capabilities'    => ['tools' => []],
            'serverInfo'      => ['name' => 'job-intell', 'version' => '1.0.0'],
        ]);
    }

    private function toolsList(mixed $id): JsonResponse
    {
        $jobOfferProperties = [
            'company'               => ['type' => 'string',  'description' => 'Company name'],
            'country'               => ['type' => 'string',  'description' => 'Country of the job'],
            'city'                  => ['type' => 'string',  'description' => 'City (optional)'],
            'position'              => ['type' => 'string',  'description' => 'Job title / position'],
            'salary_min'            => ['type' => 'integer', 'description' => 'Minimum salary'],
            'salary_max'            => ['type' => 'integer', 'description' => 'Maximum salary'],
            'salary_currency'       => ['type' => 'string',  'description' => 'ISO 4217 currency code, e.g. EUR'],
            'is_remote'             => ['type' => 'boolean', 'description' => 'true if remote position'],
            'status'                => ['type' => 'string',  'enum' => ['open', 'closed']],
            'url'                   => ['type' => 'string',  'description' => 'Link to the job posting'],
            'posted_date'           => ['type' => 'string',  'description' => 'Date posted (YYYY-MM-DD)'],
            'summary'               => ['type' => 'string',  'description' => 'Brief summary of the role and responsibilities'],
            'skills'                => ['type' => 'array',   'description' => 'Required skills list', 'items' => ['type' => 'string']],
            'notes'                 => ['type' => 'string',  'description' => 'Free-form notes'],
            'company_rating'        => ['type' => 'number',  'description' => 'Company rating from 1.0 to 5.0 (e.g. from Glassdoor)'],
            'company_rating_source' => ['type' => 'string',  'description' => 'Source of the rating, e.g. Glassdoor, Kununu, LinkedIn'],
            'company_valuation'     => ['type' => 'string',  'description' => 'Company valuation or funding stage, e.g. $1.2B, Series B, Bootstrapped'],
            'company_employees'     => ['type' => 'string',  'description' => 'Number of employees range, e.g. 51–200, 1 001–5 000'],
            'company_owners'        => ['type' => 'string',  'description' => 'Founders or key owners, e.g. Patrick Collison, John Collison'],
            'ats_probability'       => ['type' => 'string',  'enum' => ['low', 'medium', 'high'], 'description' => 'Likelihood this company uses ATS/AI CV screening: low=human review, medium=possible ATS, high=likely automated'],
        ];

        return $this->result($id, [
            'tools' => [
                [
                    'name'        => 'add_job_offer',
                    'description' => 'Add a new IT job offer to the database with full company and role details.',
                    'inputSchema' => [
                        'type'       => 'object',
                        'required'   => ['company', 'country', 'position'],
                        'properties' => $jobOfferProperties,
                    ],
                ],
                [
                    'name'        => 'update_job_offer',
                    'description' => 'Update an existing job offer by ID. Only the fields you provide will be changed.',
                    'inputSchema' => [
                        'type'       => 'object',
                        'required'   => ['id'],
                        'properties' => array_merge(
                            ['id' => ['type' => 'integer', 'description' => 'ID of the job offer to update']],
                            $jobOfferProperties,
                        ),
                    ],
                ],
                [
                    'name'        => 'list_job_offers',
                    'description' => 'Search and list job offers with optional filters.',
                    'inputSchema' => [
                        'type'       => 'object',
                        'properties' => [
                            'status'    => ['type' => 'string', 'enum' => ['open', 'closed']],
                            'country'   => ['type' => 'string'],
                            'is_remote' => ['type' => 'boolean'],
                            'search'    => ['type' => 'string', 'description' => 'Search company, position, or city'],
                            'per_page'  => ['type' => 'integer', 'description' => 'Results per page (max 100)'],
                            'page'      => ['type' => 'integer'],
                        ],
                    ],
                ],
                [
                    'name'        => 'get_insights',
                    'description' => 'Get dashboard insights: totals, top countries, cities, companies, remote ratio, monthly trend.',
                    'inputSchema' => ['type' => 'object', 'properties' => []],
                ],
            ],
        ]);
    }

    private function toolsCall(mixed $id, array $params, $user): JsonResponse
    {
        $name      = $params['name'] ?? '';
        $arguments = $params['arguments'] ?? [];

        return match ($name) {
            'add_job_offer'    => $this->addJobOffer($id, $arguments, $user),
            'update_job_offer' => $this->updateJobOffer($id, $arguments, $user),
            'list_job_offers'  => $this->listJobOffers($id, $arguments),
            'get_insights'     => $this->getInsights($id),
            default            => $this->error($id, -32602, "Unknown tool: {$name}"),
        };
    }

    private function jobOfferRules(bool $requireCore = true): array
    {
        $required = $requireCore ? 'required' : 'sometimes|required';

        return [
            'company'               => [$required, 'string', 'max:255'],
            'country'               => [$required, 'string', 'max:255'],
            'city'                  => ['nullable', 'string', 'max:255'],
            'position'              => [$required, 'string', 'max:255'],
            'salary_min'            => ['nullable', 'integer', 'min:0'],
            'salary_max'            => ['nullable', 'integer', 'min:0'],
            'salary_currency'       => ['nullable', 'string', 'size:3'],
            'is_remote'             => ['nullable', 'boolean'],
            'status'                => ['nullable', 'in:open,closed'],
            'url'                   => ['nullable', 'url'],
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

    private function addJobOffer(mixed $id, array $args, $user): JsonResponse
    {
        $validator = Validator::make($args, $this->jobOfferRules(requireCore: true));

        if ($validator->fails()) {
            return $this->error($id, -32602, 'Invalid params: ' . implode(', ', $validator->errors()->all()));
        }

        $offer = $this->service->create($validator->validated(), $user?->id);

        return $this->result($id, [
            'content' => [[
                'type' => 'text',
                'text' => "Job offer created. ID: {$offer->id}. {$offer->position} at {$offer->company} ({$offer->city}, {$offer->country})."
                    . ($offer->company_rating ? " Rating: {$offer->company_rating}/5." : '')
                    . ($offer->ats_probability ? " ATS risk: {$offer->ats_probability}." : ''),
            ]],
        ]);
    }

    private function updateJobOffer(mixed $id, array $args, $user): JsonResponse
    {
        $offerId = $args['id'] ?? null;

        if (!$offerId || !is_numeric($offerId)) {
            return $this->error($id, -32602, 'Missing or invalid required field: id');
        }

        $offer = JobOffer::find((int) $offerId);

        if (!$offer) {
            return $this->error($id, -32602, "Job offer not found: id={$offerId}");
        }

        $data      = array_diff_key($args, ['id' => true]);
        $validator = Validator::make($data, $this->jobOfferRules(requireCore: false));

        if ($validator->fails()) {
            return $this->error($id, -32602, 'Invalid params: ' . implode(', ', $validator->errors()->all()));
        }

        $offer = $this->service->update($offer, $validator->validated());

        return $this->result($id, [
            'content' => [[
                'type' => 'text',
                'text' => "Job offer updated. ID: {$offer->id}. {$offer->position} at {$offer->company}."
                    . ($offer->company_rating ? " Rating: {$offer->company_rating}/5." : '')
                    . ($offer->ats_probability ? " ATS risk: {$offer->ats_probability}." : ''),
            ]],
        ]);
    }

    private function listJobOffers(mixed $id, array $args): JsonResponse
    {
        $filters   = array_filter(array_intersect_key($args, array_flip(['status', 'country', 'is_remote', 'search'])), fn($v) => $v !== null);
        $perPage   = min((int) ($args['per_page'] ?? 20), 100);
        $paginated = $this->service->list($filters, $perPage);

        $text = "Found {$paginated->total()} job offer(s). Page {$paginated->currentPage()} of {$paginated->lastPage()}.\n\n";

        foreach ($paginated->items() as $offer) {
            $remote  = $offer->is_remote ? 'Remote' : 'Onsite';
            $salary  = $offer->salary_min ? "{$offer->salary_min}–{$offer->salary_max} {$offer->salary_currency}" : 'N/A';
            $rating  = $offer->company_rating ? " | Rating: {$offer->company_rating}/5" . ($offer->company_rating_source ? " ({$offer->company_rating_source})" : '') : '';
            $ats     = $offer->ats_probability ? " | ATS: {$offer->ats_probability}" : '';
            $skills  = !empty($offer->skills) ? " | Skills: " . implode(', ', $offer->skills) : '';
            $company = $offer->company . ($offer->company_employees ? " [{$offer->company_employees} employees]" : '') . ($offer->company_valuation ? " ({$offer->company_valuation})" : '');

            $text .= "• [ID:{$offer->id}] [{$offer->status}] {$offer->position} @ {$company} — {$offer->city}, {$offer->country} | {$remote} | Salary: {$salary}{$rating}{$ats}{$skills}\n";
        }

        return $this->result($id, [
            'content' => [['type' => 'text', 'text' => $text]],
        ]);
    }

    private function getInsights(mixed $id): JsonResponse
    {
        $data = $this->service->insights();

        $text  = "=== Job Intelligence Insights ===\n\n";
        $text .= "Total offers: {$data['total']} | Open: {$data['open']} | Closed: {$data['closed']}\n";
        $text .= "Remote: {$data['remote']} | Onsite: {$data['onsite']}\n\n";

        $text .= "Top Countries:\n";
        foreach ($data['topCountries'] as $country => $count) {
            $text .= "  {$country}: {$count}\n";
        }

        $text .= "\nTop Cities:\n";
        foreach ($data['topCities'] as $city => $count) {
            $text .= "  {$city}: {$count}\n";
        }

        $text .= "\nTop Companies:\n";
        foreach ($data['topCompanies'] as $company => $count) {
            $text .= "  {$company}: {$count}\n";
        }

        $text .= "\nMonthly Trend (last 12 months):\n";
        foreach ($data['monthlyTrend'] as $month => $count) {
            $text .= "  {$month}: {$count}\n";
        }

        return $this->result($id, [
            'content' => [['type' => 'text', 'text' => $text]],
        ]);
    }

    private function result(mixed $id, array $result): JsonResponse
    {
        return response()->json(['jsonrpc' => '2.0', 'id' => $id, 'result' => $result]);
    }

    private function error(mixed $id, int $code, string $message): JsonResponse
    {
        return response()->json([
            'jsonrpc' => '2.0',
            'id'      => $id,
            'error'   => ['code' => $code, 'message' => $message],
        ]);
    }
}
