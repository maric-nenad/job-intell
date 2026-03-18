<?php

namespace Database\Factories;

use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobOfferFactory extends Factory
{
    protected $model = JobOffer::class;

    private static array $companies = [
        'Stripe', 'Shopify', 'Gitlab', 'Notion', 'Figma', 'Vercel', 'Linear',
        'Supabase', 'PlanetScale', 'Loom', 'Miro', 'Deel', 'Remote.com',
        'Personio', 'GetYourGuide', 'Taxfix', 'N26', 'Phoebe', 'Klarna',
        'Zalando', 'Delivery Hero', 'HelloFresh', 'Tier Mobility',
    ];

    private static array $positions = [
        'Senior Backend Engineer', 'Staff Software Engineer', 'Frontend Engineer',
        'Full Stack Developer', 'DevOps Engineer', 'Platform Engineer',
        'Data Engineer', 'Machine Learning Engineer', 'Engineering Manager',
        'Senior PHP Developer', 'Go Engineer', 'Python Developer',
        'React Developer', 'Cloud Architect', 'Site Reliability Engineer',
        'Lead Software Engineer', 'TypeScript Developer', 'API Engineer',
    ];

    private static array $locations = [
        ['country' => 'Germany',     'city' => 'Berlin'],
        ['country' => 'Germany',     'city' => 'Munich'],
        ['country' => 'Germany',     'city' => 'Hamburg'],
        ['country' => 'Netherlands', 'city' => 'Amsterdam'],
        ['country' => 'Poland',      'city' => 'Warsaw'],
        ['country' => 'Poland',      'city' => 'Kraków'],
        ['country' => 'Poland',      'city' => 'Wrocław'],
        ['country' => 'Croatia',     'city' => 'Zagreb'],
        ['country' => 'UK',          'city' => 'London'],
        ['country' => 'Spain',       'city' => 'Barcelona'],
        ['country' => 'Portugal',    'city' => 'Lisbon'],
        ['country' => 'France',      'city' => 'Paris'],
        ['country' => 'Remote',      'city' => null],
    ];

    public function definition(): array
    {
        $location   = $this->faker->randomElement(self::$locations);
        $isRemote   = $location['country'] === 'Remote' || $this->faker->boolean(30);
        $salaryMin  = $this->faker->randomElement([60, 70, 80, 90, 100, 110, 120]) * 1000;
        $salaryMax  = $salaryMin + $this->faker->randomElement([10, 15, 20, 30]) * 1000;
        $currency   = $location['country'] === 'Poland' ? 'PLN' : ($location['country'] === 'UK' ? 'GBP' : 'EUR');

        return [
            'company'         => $this->faker->randomElement(self::$companies),
            'country'         => $isRemote && $location['country'] === 'Remote' ? 'Remote' : $location['country'],
            'city'            => $location['city'],
            'position'        => $this->faker->randomElement(self::$positions),
            'salary_min'      => $salaryMin,
            'salary_max'      => $salaryMax,
            'salary_currency' => $currency,
            'is_remote'       => $isRemote,
            'status'          => $this->faker->randomElement(['open', 'open', 'open', 'closed']),
            'url'             => $this->faker->optional(0.7)->url(),
            'posted_date'     => $this->faker->dateTimeBetween('-6 months', 'now'),
            'notes'           => $this->faker->optional(0.4)->sentence(),
        ];
    }
}
