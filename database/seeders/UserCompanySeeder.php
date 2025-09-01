<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

class UserCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menghubungkan user dengan company untuk multi-tenancy
     * tanpa mengubah data user yang sudah ada
     */
    public function run(): void
    {
        // Ambil semua user yang sudah ada
        $users = User::all();
        
        // Ambil semua company yang sudah ada
        $companies = Company::all();
        
        if ($users->isEmpty() || $companies->isEmpty()) {
            $this->command->info('Tidak ada user atau company yang tersedia untuk dihubungkan');
            return;
        }
        
        // Hubungkan semua user dengan semua company
        foreach ($users as $user) {
            // Cek role user
            $isAdmin = $user->hasRole('Super Admin');
            
            if ($isAdmin) {
                // Admin dapat mengakses semua company
                foreach ($companies as $company) {
                    if (!$user->companies()->where('company_id', $company->id)->exists()) {
                        $user->companies()->attach($company->id);
                        $this->command->info("Admin {$user->name} ({$user->email}) berhasil dihubungkan dengan company {$company->name}");
                    }
                }
            } else {
                // User non-admin hanya terhubung dengan company pertama sebagai default
                $defaultCompany = $companies->first();
                if (!$user->companies()->where('company_id', $defaultCompany->id)->exists()) {
                    $user->companies()->attach($defaultCompany->id);
                    $this->command->info("User {$user->name} ({$user->email}) berhasil dihubungkan dengan company {$defaultCompany->name}");
                }
            }
        }
        
        $this->command->info('Seeder UserCompany berhasil dijalankan!');
    }
}
