<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Company;


class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $user = User::first();

        Company::create([
            'id' => Str::uuid(),
            'name' => 'PT. HAS Survey Geospasial',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Company::create([
            'id' => Str::uuid(),
            'name' => 'CV HAS Survey',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
