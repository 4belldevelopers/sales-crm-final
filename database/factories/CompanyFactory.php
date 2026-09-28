<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'company_email' => fake()->unique()->companyEmail(),
            'company_phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'address' => fake()->address(),
            'status' => 'active',
            'package_type' => 'monthly',
        ];
    }
}
