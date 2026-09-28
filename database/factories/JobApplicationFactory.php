<?php

namespace Database\Factories;

use App\Models\JobApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition(): array
    {
        return [
            'submitted_by' => 1,
            'company_id' => 1,
            'thirdparty_company_id' => 0,
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'current_location' => fake()->city(),
            'current_designation' => fake()->jobTitle(),
            'current_company' => fake()->company(),
            'total_exp' => '5 years',
            'rel_exp' => '3 years',
            'skills' => 'PHP, Laravel, Elasticsearch, MySQL, Redis',
            'rating' => '4',
            'status_id' => 1,
            'master_status_id' => 1,
            'child_status_id' => 1,
            'ctc' => 1200000,
            'ectc' => 1500000,
            'current_ctc_cur' => 0,
            'current_ctc_mode' => 0,
            'current_ectc_cur' => 0,
            'current_ectc_mode' => 0,
            'recruiter_note' => 'Candidate exhibits strong system architecture and distributed systems skills.',
            'aggregate_version' => 1,
            'resume_version' => 1,
        ];
    }

    /**
     * Create candidate fixture populated with all candidate attributes per Section 4 budget testing.
     */
    public function createWithAllAttributes(array $attributes = []): JobApplication
    {
        $allAttributes = array_merge([
            'submitted_by' => 1,
            'company_id' => 1,
            'thirdparty_company_id' => 0,
            'full_name' => 'Alexander Pierce',
            'email' => 'alexander.pierce@example.com',
            'phone_code' => '+1',
            'phone' => '5550198273',
            'secondrary_email' => 'alex.personal@example.com',
            'secondary_mobile' => '5550198274',
            'gender' => 'Male',
            'dob' => '1990-05-15',
            'country' => 'United States',
            'state' => 'California',
            'city' => 'San Francisco',
            'current_location' => 'San Francisco, CA',
            'current_designation' => 'Principal Software Engineer',
            'current_company' => 'Acme Cloud Technologies',
            'total_exp' => '12 years',
            'rel_exp' => '8 years',
            'skills' => 'PHP, Laravel, MySQL, Elasticsearch, Redis, Octane, FrankenPHP, Docker, Kubernetes',
            'rating' => '5',
            'status_id' => 2,
            'master_status_id' => 2,
            'child_status_id' => 3,
            'ctc' => 185000,
            'current_ctc_cur' => 4, // USD
            'current_ctc_mode' => 2, // Yearly
            'ectc' => 210000,
            'current_ectc_cur' => 4,
            'current_ectc_mode' => 2,
            'proposed_ctc' => 200000,
            'offer_amount' => 205000,
            'offer_currency' => 4,
            'offer_mode' => 2,
            'recruiter_note' => 'Top tier candidate with deep CQRS and distributed systems expertise. Outstanding communication during technical rounds.',
            'careersummary' => '12+ years of engineering high-throughput SaaS platforms and event-driven architectures.',
            'aggregate_version' => 1,
            'resume_version' => 1,
            'resume' => 'Alexander_Pierce_Resume_2026.pdf',
        ], $attributes);

        return $this->create($allAttributes);
    }
}
