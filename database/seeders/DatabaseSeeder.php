<?php

namespace Database\Seeders;

use App\Models\Application;
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

        // 60 job offers spread across the last 6 months
        $offers = JobOffer::factory(60)->create(['user_id' => $user->id]);

        // Track ~20 applications for the user across different pipeline stages
        $statuses = ['saved', 'saved', 'applied', 'applied', 'applied', 'screening', 'screening', 'interview', 'interview', 'offer', 'rejected', 'rejected', 'withdrawn'];

        $offers->random(20)->each(function (JobOffer $offer) use ($user, $statuses) {
            $status = fake()->randomElement($statuses);
            Application::create([
                'user_id'        => $user->id,
                'job_offer_id'   => $offer->id,
                'status'         => $status,
                'applied_date'   => in_array($status, ['applied', 'screening', 'interview', 'offer', 'rejected', 'withdrawn'])
                                        ? fake()->dateTimeBetween('-3 months', '-1 week')
                                        : null,
                'next_follow_up' => fake()->optional(0.5)->dateTimeBetween('now', '+2 weeks'),
                'notes'          => fake()->optional(0.6)->sentence(),
            ]);
        });
    }
}
