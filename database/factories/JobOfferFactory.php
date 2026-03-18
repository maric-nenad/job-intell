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

    private static array $summaries = [
        'Join a fast-growing fintech team building the next generation of payment infrastructure. You will own critical backend services, contribute to API design, and work closely with product to shape the roadmap. We value autonomy, async-first communication, and pragmatic engineering.',
        'We are looking for a pragmatic engineer to help scale our platform to millions of users. The role spans both backend and infrastructure work — expect a mix of feature development, performance tuning, and on-call ownership. Strong PostgreSQL and distributed systems experience preferred.',
        'Exciting opportunity to join a remote-first product company at Series B. You will be embedded in a cross-functional squad with a product manager, designer, and two other engineers. Tech stack: TypeScript, React, Go, Kubernetes. Ownership is high and shipping cadence is weekly.',
        'Work on developer tooling used by thousands of engineers every day. The team is small, highly senior, and moves fast. You will design and implement core platform features, review architecture proposals, and mentor mid-level engineers. OSS background is a big plus.',
        'Help build a data platform that processes billions of events per month. Strong focus on reliability and observability. You will write Go microservices, design Kafka pipelines, and collaborate with data science on ML feature pipelines. Experience with Spark or Flink is a bonus.',
        'A product-led growth company seeking a full-stack engineer to own the user onboarding funnel. You will instrument analytics, run A/B experiments, improve activation rates, and ship UI improvements in React. Comfort with data-driven decision making is essential.',
        'Be an early engineering hire at a pre-Series A startup disrupting B2B procurement. Full ownership of the backend (Laravel/PHP), opportunity to grow into a lead role as the team scales. Founders are ex-Stripe and ex-Shopify. Competitive equity package.',
        'Senior role at an established European e-commerce platform. You will modernise a legacy PHP monolith to event-driven microservices. Expect deep technical challenges around migration strategy, data consistency, and zero-downtime deployments.',
        'Platform engineering role focused on internal developer experience. Build and maintain CI/CD pipelines, internal tooling, and self-service infrastructure. You will define the golden path for all product teams. Heavy use of Terraform, GitHub Actions, and AWS.',
        'Machine learning engineer to productionise models for a real-time recommendation engine. Bridge the gap between data science research and robust production systems. Strong Python required; experience with MLflow, Ray, or Kubeflow is a plus.',
    ];

    private static array $skillPool = [
        'PHP', 'Laravel', 'Go', 'Python', 'TypeScript', 'JavaScript', 'React', 'Vue.js',
        'Node.js', 'PostgreSQL', 'MySQL', 'Redis', 'Kafka', 'RabbitMQ', 'Elasticsearch',
        'Docker', 'Kubernetes', 'Terraform', 'AWS', 'GCP', 'Azure', 'Linux',
        'REST APIs', 'GraphQL', 'gRPC', 'Microservices', 'CI/CD', 'GitHub Actions',
        'Git', 'Agile', 'TDD', 'DDD', 'Event Sourcing', 'CQRS',
        'Machine Learning', 'PyTorch', 'Spark', 'dbt', 'Airflow',
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
            'summary'            => $this->faker->optional(0.75)->randomElement(self::$summaries),
            'skills'             => $this->faker->optional(0.8)->randomElements(self::$skillPool, $this->faker->numberBetween(3, 8)),
            'company_valuation'  => $this->faker->optional(0.6)->randomElement([
                'Bootstrapped', 'Pre-seed', 'Seed', 'Series A', 'Series B', 'Series C',
                '$50M', '$120M', '$500M', '$1.2B', '$3B', '$10B+', 'Public (NYSE)', 'Public (NASDAQ)',
            ]),
            'company_employees'  => $this->faker->optional(0.7)->randomElement([
                '1–10', '11–50', '51–200', '201–500', '501–1 000', '1 001–5 000', '5 001–10 000', '10 000+',
            ]),
            'company_owners'     => $this->faker->optional(0.5)->randomElement([
                'Patrick Collison, John Collison',
                'Tobias Lütke',
                'Sid Sijbrandij',
                'Ivan Zhao, Simon Last',
                'Dylan Field, Evan Wallace',
                'Guillermo Rauch',
                'Karri Saarinen',
                'Paul Copplestone, Ant Wilson',
                'Sam Lambert, Nick Van Wiggeren',
                'Shahed Khan, Vinay Hiremath',
                'Andrey Khusid',
            ]),
            'company_rating'        => $this->faker->optional(0.6)->randomElement([3.2, 3.5, 3.8, 4.0, 4.1, 4.2, 4.4, 4.6, 4.8]),
            'company_rating_source' => $this->faker->optional(0.6)->randomElement(['Glassdoor', 'Kununu', 'LinkedIn', 'Blind']),
            'ats_probability'       => $this->faker->optional(0.65)->randomElement(['low', 'low', 'medium', 'medium', 'high']),
            'posted_date'           => $this->faker->dateTimeBetween('-6 months', 'now'),
            'notes'                 => $this->faker->optional(0.4)->sentence(),
        ];
    }
}
