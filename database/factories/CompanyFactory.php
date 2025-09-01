<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $companies = [
            'CV HAS Survey',
            'PT. HAS Survey Geospasial'
        ];

        return [
            'name' => $this->faker->randomElement($companies),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
