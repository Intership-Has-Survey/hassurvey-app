<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('seeders/csv/nama_bank.csv');

        if (!File::exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan di: " . $csvPath);
            return;
        }

        // Baca file CSV
        $file = fopen($csvPath, 'r');

        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $namaBank = $row[1] ?? null;

            if ($namaBank) {
                $namaBankClean = trim($namaBank);

                Bank::updateOrCreate(
                    ['nama_bank' => $namaBankClean]
                );
            }
        }

        fclose($file);
    }
}
