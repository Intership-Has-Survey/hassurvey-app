<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penjualan;
use App\Models\Corporate;
use App\Models\Perorangan;
use App\Models\Sales;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class PenjualanSeeder extends Seeder
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

        // Create 40 penjualan records with random created_at from 2023-01-01 to now
        $startDate = Carbon::create(2023, 1, 1);
        $endDate = Carbon::now();

        $statuses = ['lunas', 'belum_lunas', 'sebagian'];
        
        // Get available sales
        $sales = Sales::inRandomOrder()->limit(8)->get();
        if ($sales->isEmpty()) {
            $sales = Sales::factory()->count(8)->create();
        }
        
        // Create penjualan for corporate customers
        $corporates = Corporate::inRandomOrder()->limit(12)->get();
        if ($corporates->isEmpty()) {
            $corporates = Corporate::factory()->count(12)->create();
        }
        
        foreach ($corporates as $corporate) {
            $penjualanCount = rand(2, 4);
            for ($i = 0; $i < $penjualanCount; $i++) {
                $createdAt = $this->randomDate($startDate, $endDate);
                $company = $companies->random();
                $salesPerson = $sales->random();
                $user = $users->random();
                
                Penjualan::create([
                    'nama_penjualan' => 'Penjualan ' . fake()->words(3, true),
                    'tanggal_penjualan' => $createdAt->copy()->addDays(rand(1, 30)),
                    'corporate_id' => $corporate->id,
                    'sales_id' => $salesPerson->id,
                    'status_pembayaran' => fake()->randomElement($statuses),
                    'catatan' => fake()->sentence(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                ]);
            }
        }

        // Create penjualan for perorangan customers
        $perorangans = Perorangan::inRandomOrder()->limit(8)->get();
        if ($perorangans->isEmpty()) {
            $perorangans = Perorangan::factory()->count(8)->create();
        }
        
        foreach ($perorangans as $perorangan) {
            $penjualanCount = rand(1, 3);
            for ($i = 0; $i < $penjualanCount; $i++) {
                $createdAt = $this->randomDate($startDate, $endDate);
                $company = $companies->random();
                $salesPerson = $sales->random();
                $user = $users->random();
                
                $penjualan = Penjualan::create([
                    'nama_penjualan' => 'Penjualan ' . fake()->words(3, true),
                    'tanggal_penjualan' => $createdAt->copy()->addDays(rand(1, 30)),
                    'sales_id' => $salesPerson->id,
                    'status_pembayaran' => fake()->randomElement($statuses),
                    'catatan' => fake()->sentence(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                ]);
                
                // Attach perorangan to penjualan
                $penjualan->perorangan()->attach($perorangan->id, [
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
