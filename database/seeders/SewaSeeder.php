<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sewa;
use Carbon\Carbon;

class SewaSeeder extends Seeder
{
    public function run()
    {
        // Create 50 sewa records with random created_at from 2023-01-01 to now
        $startDate = Carbon::create(2023, 1, 1);
        $endDate = Carbon::now();

        Sewa::factory()->count(50)->create()->each(function ($sewa) use ($startDate, $endDate) {
            $sewa->created_at = $this->randomDate($startDate, $endDate);
            $sewa->updated_at = $sewa->created_at;
            $sewa->save();

            // Assign customer based on customer_flow_type
            if ($sewa->customer_flow_type === 'corporate') {
                $corporate = $sewa->corporate()->first();
                if (!$corporate) {
                    $corporate = \App\Models\Corporate::factory()->create();
                    $sewa->corporate()->associate($corporate);
                    $sewa->save();
                }
            } else {
                $perorangan = $sewa->perorangan()->first();
                if (!$perorangan) {
                    $perorangan = \App\Models\Perorangan::factory()->create();
                    $sewa->perorangan()->attach($perorangan->id);
                }
            }
        });
    }

    private function randomDate($startDate, $endDate)
    {
        $min = $startDate->timestamp;
        $max = $endDate->timestamp;
        $val = mt_rand($min, $max);
        return Carbon::createFromTimestamp($val);
    }
}
