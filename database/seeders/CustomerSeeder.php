<?php

// namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;
// use App\Models\Customer;
// use App\Models\User;

// class CustomerSeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
//         $user = User::first();

//         if (!$user) {
//             $user = User::factory()->create();
//         }

//         Customer::create([
//             'nama' => 'PT. Maju Mundur',
//             'email' => 'maju@gmail.com',
//             'telepon' => '085161648718',
//             'provinsi' => '31', // DKI Jakarta
//             'kota' => '31.71', // Kota Jakarta Pusat
//             'kecamatan' => '31.71.01', // Gambir
//             'desa' => '31.71.01.1001', // Gambir
//             'detail_alamat' => 'Jl. Raya No. 1',
//             'user_id' => $user->id,
//         ]);
//         Customer::create([
//             'nama' => 'PT. Jaya Abadi',
//             'email' => 'jaya@gmail.com',
//             'telepon' => '085161648719',
//             'provinsi' => '32', // Jawa Barat
//             'kota' => '32.73', // Kota Bandung
//             'kecamatan' => '32.73.01', // Bandung Kulon
//             'desa' => '32.73.01.1001', // Cigondewah Kaler
//             'detail_alamat' => 'Jl. Merdeka No. 2',
//             'user_id' => $user->id,
//         ]);
//         Customer::create([
//             'nama' => 'CV. Sukses Selalu',
//             'email' => 'sukses@gmail.com',
//             'telepon' => '085161648720',
//             'provinsi' => '35', // Jawa Timur
//             'kota' => '35.78', // Kota Surabaya
//             'kecamatan' => '35.78.01', // Asemrowo
//             'desa' => '35.78.01.1001', // Asemrowo
//             'detail_alamat' => 'Jl. Kebangsaan No. 3',
//             'user_id' => $user->id,
//         ]);
//         Customer::create([
//             'nama' => 'PT. Bersama Kita',
//             'email' => 'bersama@gmail.com',
//             'telepon' => '085161648721',
//             'provinsi' => '34', // DI Yogyakarta
//             'kota' => '34.71', // Kota Yogyakarta
//             'kecamatan' => '34.71.01', // Danurejan
//             'desa' => '34.71.01.1001', // Bausasran
//             'detail_alamat' => 'Jl. Kebangsaan No. 4',
//             'user_id' => $user->id,
//         ]);
//         Customer::create([
//             'nama' => 'PT. Sejahtera Bersama',
//             'email' => 'sejahtera@gmail.com',
//             'telepon' => '085161648722',
//             'provinsi' => '12', // Sumatera Utara
//             'kota' => '12.71', // Kota Medan
//             'kecamatan' => '12.71.01', // Medan Amplas
//             'desa' => '12.71.01.1001', // Amplasa
//             'detail_alamat' => 'Jl. Kebangsaan No. 5',
//             'user_id' => $user->id,
//         ]);
//     }
// }
