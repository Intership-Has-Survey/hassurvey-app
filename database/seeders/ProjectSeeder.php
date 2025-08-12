<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Personel;
use App\Models\Perorangan;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        // Ensure we have users
        $users = User::all();
        if ($users->count() < 1) {
            $users = User::factory()->count(5)->create();
        }

        // Get available companies
        $companies = Company::all();
        if ($companies->count() < 2) {
            $companies = Company::factory()->count(2)->create();
        }

        // Create 50 projects with random created_at from 2023-01-01 to now
        $startDate = Carbon::create(2023, 1, 1);
        $endDate = Carbon::now();

        Project::factory()->count(50)->create([
            'user_id' => $users->random()->id
        ])->each(function ($project) use ($startDate, $endDate, $companies) {
            $project->created_at = $this->randomDate($startDate, $endDate);
            $project->updated_at = $project->created_at;
            $project->company_id = $companies->random()->id;
            $project->save();

            // Assign personels with roles
            $personelRoles = ['Surveyor', 'Asisten Surveyor', 'Driver', 'Drafter'];
            $personels = Personel::inRandomOrder()->limit(count($personelRoles))->get();

            if ($personels->count() < count($personelRoles)) {
                // Create missing personels
                $missingCount = count($personelRoles) - $personels->count();
                $newPersonels = Personel::factory()->count($missingCount)->create();
                $personels = $personels->concat($newPersonels);
            }

            $syncData = [];
            foreach ($personels as $index => $personel) {
                $syncData[$personel->id] = [
                    'peran' => $personelRoles[$index],
                    'tanggal_mulai' => now(),
                    'user_id' => $project->user_id,
                ];
            }

            $project->personels()->sync($syncData);

            // Assign perorangan customers
            $perorangan = Perorangan::inRandomOrder()->limit(1)->get();
            $project->perorangan()->sync($perorangan->pluck('id')->toArray());
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
