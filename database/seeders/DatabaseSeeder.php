<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Cv;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@jobintell.com'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );

        // CVs (placeholder file data for seeding purposes)
        $cvs = collect([
            Cv::firstOrCreate(
                ['user_id' => $user->id, 'name' => 'Senior Backend — 2025'],
                ['description' => '7 years backend, PHP + Go', 'file_path' => 'cvs/placeholder.pdf', 'file_name' => 'senior-backend-2025.pdf', 'file_size' => 0]
            ),
            Cv::firstOrCreate(
                ['user_id' => $user->id, 'name' => 'Full Stack — Concise'],
                ['description' => 'React, TypeScript, Laravel', 'file_path' => 'cvs/placeholder.pdf', 'file_name' => 'fullstack-concise.pdf', 'file_size' => 0]
            ),
            Cv::firstOrCreate(
                ['user_id' => $user->id, 'name' => 'Lead / Manager Track'],
                ['description' => 'Engineering lead, teams of 5–12', 'file_path' => 'cvs/placeholder.pdf', 'file_name' => 'lead-manager.pdf', 'file_size' => 0]
            ),
        ]);

        // 60 job offers
        $offers = JobOffer::factory(60)->create(['user_id' => $user->id]);

        $stageWeights = [
            'saved', 'saved',
            'preparation',
            'applied', 'applied', 'applied',
            'screening', 'screening',
            'interview', 'interview',
            'offer',
            'rejected', 'rejected',
            'withdrawn',
        ];

        $interviewerPool = [
            'Sarah Kim', 'James Okafor', 'Lena Müller', 'Tom Reyes',
            'Priya Nair', 'David Horak', 'Marta Kowalski', 'Alex Chen',
        ];

        $offers->random(22)->each(function (JobOffer $offer) use ($user, $cvs, $stageWeights, $interviewerPool) {
            $status    = fake()->randomElement($stageWeights);
            $appliedAt = in_array($status, ['applied', 'screening', 'interview', 'offer', 'rejected', 'withdrawn'])
                ? fake()->dateTimeBetween('-3 months', '-3 weeks')
                : null;

            $data = [
                'user_id'          => $user->id,
                'job_offer_id'     => $offer->id,
                'status'           => $status,
                'status_changed_at'=> fake()->dateTimeBetween('-3 months', 'now'),
                'cv_id'            => in_array($status, ['applied', 'screening', 'interview', 'offer'])
                                        ? $cvs->random()->id
                                        : null,
                'applied_date'     => $appliedAt,
                'next_follow_up'   => fake()->optional(0.4)->dateTimeBetween('now', '+2 weeks'),
                'notes'            => fake()->optional(0.55)->randomElement([
                    'Referral from a former colleague.',
                    'Very interesting stack — Go + Kafka.',
                    'Remote-first, great culture reviews on Glassdoor.',
                    'Salary range is on the lower end.',
                    'Reached out to hiring manager on LinkedIn.',
                    'Strong mission alignment.',
                    'Heard back within 24 hours — fast process.',
                ]),
            ];

            if (in_array($status, ['screening', 'interview', 'offer', 'rejected', 'withdrawn'])) {
                $data['screening_time']    = fake()->dateTimeBetween('-2 months', '-1 week');
                $data['screening_contact'] = fake()->randomElement([
                    'Emma Larson (Recruiter)',
                    'HR Team <hr@company.com>',
                    'Carlos Bento (Talent Acquisition)',
                    'Sophie Walker (People Ops)',
                ]);
            }

            if (in_array($status, ['interview', 'offer', 'rejected', 'withdrawn'])) {
                $data['interview_time']    = fake()->dateTimeBetween('-6 weeks', '-3 days');
                $data['interview_contact'] = fake()->randomElement([
                    'Mark Jensen (Eng Manager)',
                    'CTO Office',
                    'Laura Dubois (Lead Engineer)',
                    'Hiring Panel',
                ]);
                $data['interview_interviewers'] = fake()->randomElements($interviewerPool, fake()->numberBetween(1, 3));
            }

            if (in_array($status, ['offer'])) {
                $salaryOffer = fake()->randomElement([85, 90, 95, 100, 110, 120, 130]) * 1000;
                $data['offer_salary']   = number_format($salaryOffer) . ' ' . fake()->randomElement(['EUR', 'USD', 'GBP']);
                $data['offer_benefits'] = fake()->randomElement([
                    "25 days PTO\nHealth & dental insurance\nHome office budget €1 000\nStock options (0.1%)",
                    "Unlimited PTO\nFull remote\nLearning budget $2 000/yr\n4-day work week pilot",
                    "30 days PTO\nGym membership\nEquipment budget €2 500\nAnnual bonus up to 15%",
                    "20 days PTO\nPrivate health plan\nFlexible hours\nConference budget €1 500",
                ]);
            }

            if ($status === 'rejected') {
                $data['rejected_at'] = fake()->dateTimeBetween('-4 weeks', 'now');
            }

            if ($status === 'withdrawn') {
                $data['withdrawn_at'] = fake()->dateTimeBetween('-4 weeks', 'now');
            }

            Application::create($data);
        });
    }
}
