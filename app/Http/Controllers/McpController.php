<?php

namespace App\Http\Controllers;

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
        return $this->result($id, [
            'tools' => [
                [
                    'name'        => 'add_job_offer',
                    'description' => 'Add a new IT job offer to the database.',
                    'inputSchema' => [
                        'type'       => 'object',
                        'required'   => ['company', 'country', 'position'],
                        'properties' => [
                            'company'         => ['type' => 'string', 'description' => 'Company name'],
                            'country'         => ['type' => 'string', 'description' => 'Country of the job'],
                            'city'            => ['type' => 'string', 'description' => 'City (optional)'],
                            'position'        => ['type' => 'string', 'description' => 'Job title / position'],
                            'salary_min'      => ['type' => 'integer', 'description' => 'Minimum salary'],
                            'salary_max'      => ['type' => 'integer', 'description' => 'Maximum salary'],
                            'salary_currency' => ['type' => 'string', 'description' => 'ISO 4217 currency code, e.g. EUR'],
                            'is_remote'       => ['type' => 'boolean', 'description' => 'true if remote position'],
                            'status'          => ['type' => 'string', 'enum' => ['open', 'closed']],
                            'url'             => ['type' => 'string', 'description' => 'Link to the job posting'],
                            'posted_date'     => ['type' => 'string', 'description' => 'Date posted (YYYY-MM-DD)'],
                            'notes'           => ['type' => 'string', 'description' => 'Free-form notes'],
                        ],
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
            'add_job_offer'   => $this->addJobOffer($id, $arguments, $user),
            'list_job_offers' => $this->listJobOffers($id, $arguments),
            'get_insights'    => $this->getInsights($id),
            default           => $this->error($id, -32602, "Unknown tool: {$name}"),
        };
    }

    private function addJobOffer(mixed $id, array $args, $user): JsonResponse
    {
        $validator = Validator::make($args, [
            'company'         => ['required', 'string', 'max:255'],
            'country'         => ['required', 'string', 'max:255'],
            'city'            => ['nullable', 'string', 'max:255'],
            'position'        => ['required', 'string', 'max:255'],
            'salary_min'      => ['nullable', 'integer', 'min:0'],
            'salary_max'      => ['nullable', 'integer', 'min:0'],
            'salary_currency' => ['nullable', 'string', 'size:3'],
            'is_remote'       => ['nullable', 'boolean'],
            'status'          => ['nullable', 'in:open,closed'],
            'url'             => ['nullable', 'url'],
            'posted_date'     => ['nullable', 'date'],
            'notes'           => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error($id, -32602, 'Invalid params: ' . implode(', ', $validator->errors()->all()));
        }

        $offer = $this->service->create($validator->validated(), $user?->id);

        return $this->result($id, [
            'content' => [[
                'type' => 'text',
                'text' => "Job offer created successfully. ID: {$offer->id}. Position: {$offer->position} at {$offer->company} ({$offer->country}).",
            ]],
        ]);
    }

    private function listJobOffers(mixed $id, array $args): JsonResponse
    {
        $filters  = array_filter(array_intersect_key($args, array_flip(['status', 'country', 'is_remote', 'search'])), fn($v) => $v !== null);
        $perPage  = min((int) ($args['per_page'] ?? 20), 100);
        $paginated = $this->service->list($filters, $perPage);

        $text = "Found {$paginated->total()} job offer(s). Page {$paginated->currentPage()} of {$paginated->lastPage()}.\n\n";
        foreach ($paginated->items() as $offer) {
            $remote = $offer->is_remote ? 'Remote' : 'Onsite';
            $salary = $offer->salary_min ? "{$offer->salary_min}–{$offer->salary_max} {$offer->salary_currency}" : 'N/A';
            $text  .= "• [{$offer->status}] {$offer->position} @ {$offer->company} — {$offer->city}, {$offer->country} | {$remote} | Salary: {$salary}\n";
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
