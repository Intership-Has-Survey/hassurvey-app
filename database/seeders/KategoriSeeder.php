<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\User;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create();
        }

        Kategori::create([
            'nama' => 'Topografi',
            'keterangan' => 'Pemetaan topografi adalah proses pengukuran dan penggambaran detail bentuk permukaan daratan, baik fitur alami seperti gunung dan sungai, maupun fitur buatan seperti jalan dan bangunan. Ciri utamanya adalah penggunaan garis kontur yang menghubungkan titik-titik dengan ketinggian yang sama, sehingga peta dapat secara visual merepresentasikan kecuraman atau kelandaian suatu medan. Informasi ini menjadi dasar fundamental untuk perencanaan wilayah, rekayasa sipil, dan analisis lingkungan.',
            'user_id' => $user->id,
        ]);

        Kategori::create([
            'nama' => 'Mine Survey',
            'keterangan' => 'Survei tambang atau mine survey adalah penerapan ilmu pemetaan yang dikhususkan untuk industri pertambangan. Kegiatannya meliputi penentuan batas wilayah izin usaha, perhitungan volume galian dan timbunan secara akurat untuk memantau produksi, serta pemetaan cadangan mineral yang tersisa. Survei ini juga berperan krusial dalam memantau stabilitas lereng demi menjamin keselamatan operasional di seluruh siklus pertambangan.',
            'user_id' => $user->id,
        ]);

        Kategori::create([
            'nama' => 'Drone (Aerial Mapping)',
            'keterangan' => 'Pemetaan udara menggunakan drone adalah metode akuisisi data geospasial modern dengan menerbangkan wahana tanpa awak di jalur terprogram. Drone mengambil ratusan hingga ribuan foto resolusi tinggi yang saling tumpang tindih, yang kemudian diolah menjadi produk akurat seperti peta foto (orthomosaic), model permukaan digital (DSM), dan peta kontur. Metode ini menawarkan efisiensi tinggi dari segi waktu, biaya, dan tingkat detail untuk berbagai keperluan pemetaan.',
            'user_id' => $user->id,
        ]);

        Kategori::create([
            'nama' => 'Bathimetri',
            'keterangan' => 'Batimetri pada dasarnya adalah "topografi bawah air", yaitu ilmu yang berfokus pada pengukuran dan pemetaan kedalaman serta bentuk dasar perairan seperti laut, danau, atau sungai. Pengukuran umumnya dilakukan dengan teknologi sonar (echosounder) yang dipasang di kapal untuk mengirimkan gelombang suara ke dasar perairan. Hasilnya adalah peta batimetri yang sangat penting untuk keselamatan navigasi, perencanaan infrastruktur laut, dan eksplorasi sumber daya alam.',
            'user_id' => $user->id,
        ]);

        Kategori::create([
            'nama' => 'PTSL',
            'keterangan' => 'PTSL adalah program strategis pemerintah Indonesia yang bertujuan untuk mempercepat proses pendaftaran dan sertifikasi seluruh bidang tanah di suatu wilayah secara serentak. Tujuannya adalah untuk memberikan kepastian hukum dan perlindungan hak atas tanah kepada masyarakat, mengurangi sengketa, serta membangun basis data pertanahan yang lengkap dan akurat untuk seluruh Indonesia.',
            'user_id' => $user->id,
        ]);

        Kategori::create([
            'nama' => 'CUT N FILL',
            'keterangan' => 'Cut and Fill atau gali dan urug adalah proses rekayasa dalam konstruksi untuk mempersiapkan lahan dengan cara memindahkan material tanah. Proses ini melibatkan pengerukan tanah dari area yang elevasinya terlalu tinggi (cut) dan menggunakannya untuk menimbun area yang elevasinya terlalu rendah (fill) hingga mencapai ketinggian yang diinginkan sesuai desain. Perhitungan volume yang akurat sangat penting untuk efisiensi biaya dan keseimbangan material dalam proyek infrastruktur.',
            'user_id' => $user->id,
        ]);
    }
}
