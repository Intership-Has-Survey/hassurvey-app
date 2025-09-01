<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kalibrasi;
use App\Models\Corporate;
use App\Models\Perorangan;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class KalibrasiSeeder extends Seeder
{
    public function run()
    {
        // Ensure we have users to reference
        $users = User::all();
        if ($users->isEmpty()) {
            // Create a default user if none exist
            $user = User::create([
                'name' => 'Default User',
                'email' => 'default@example.com',
                'password' => bcrypt('password'),
            ]);
            $users = collect([$user]);
        }

        // Get available companies
        $companies = Company::all();
        if ($companies->count() < 2) {
            // Create 2 companies if they don't exist
            $companies = Company::factory()->count(2)->create();
        }

        // Create 30 kalibrasi records with random created_at from 2023-01-01 to now
        $startDate = Carbon::create(2023, 1, 1);
        $endDate = Carbon::now();

        $statuses = ['dalam_proses', 'selesai', 'batal'];
        
        // Create kalibrasi for corporate customers
        $corporates = Corporate::inRandomOrder()->limit(10)->get();
        if ($corporates->isEmpty()) {
            $corporates = Corporate::factory()->count(10)->create();
        }
        
        foreach ($corporates as $corporate) {
            $kalibrasiCount = rand(2, 5);
            for ($i = 0; $i < $kalibrasiCount; $i++) {
                $createdAt = $this->randomDate($startDate, $endDate);
                $company = $companies->random();
                $user = $users->random();
                
                Kalibrasi::create([
                    'nama' => 'Kalibrasi Alat ' . fake()->word() . ' ' . strtoupper(fake()->randomLetter()),
                    'corporate_id' => $corporate->id,
                    'harga' => fake()->randomFloat(2, 500000, 5000000),
                    'status' => fake()->randomElement($statuses),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                ]);
            }
        }

        // Create kalibrasi for perorangan customers
        $perorangans = Perorangan::inRandomOrder()->limit(5)->get();
        if ($perorangans->isEmpty()) {
            $perorangans = Perorangan::factory()->count(5)->create();
        }
        
        foreach ($perorangans as $perorangan) {
            $kalibrasiCount = rand(1, 3);
            for ($i = 0; $i < $kalibrasiCount; $i++) {
                $createdAt = $this->randomDate($startDate, $endDate);
                $company = $companies->random();
                $user = $users->random();
                
                $kalibrasi = Kalibrasi::create([
                    'nama' => 'Kalibrasi Alat ' . fake()->word() . ' ' . strtoupper(fake()->randomLetter()),
                    'harga' => fake()->randomFloat(2, 300000, 2000000),
                    'status' => fake()->randomElement($statuses),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                ]);
                
                // Attach perorangan to kalibrasi
                $kalibrasi->perorangan()->attach($perorangan->id, [
                    'peran' => 'customer',
                ]);
            }
        }
    }

    private function randomDate($startDate, $endDate)
    {
        $min = $startDate->timestamp;
        $max = $endDate->timestamp;
        $val = mt_rand($min, $max);
        return Carbon::createFromTimestamp($val);
    }
}
