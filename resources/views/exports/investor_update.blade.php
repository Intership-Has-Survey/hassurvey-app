<!DOCTYPE html>
<html>

<head>
    {{-- biru muda has : c5d9f0 --}}
    {{-- kuning has : ffc000 --}}
    {{-- abu has : ddd9c4 --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">s
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <title>Pengajuan PDF</title>
    <style>
        body {
            font-family: "Open Sans", sans-serif;
            font-weight: normal;
        }

        .page-break {
            page-break-before: always;
        }

        .kepala {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* mengambil dua kelas dan menjadikannya 1 flex */
        .kepala-kanan,
        .kepala-kiri {
            flex: 1;
        }

        /* lebih besar dari yang di atas */
        .kepala-tengah {
            text-align: center;
            line-height: 100%;
            flex: 3;
            font-size: 12px;
        }

        .kalender {
            text-align: center;
            font-size: 12px;
            line-height: 100%;
        }

        .infoalat {
            font-size: 12px;
            line-height: 100%;
            display: flex;
            justify-content: space-between;
        }

        .infoalat-jenis {
            flex: 1;
        }

        .infoalat-kode {
            flex: 1;
            display: flex;
            justify-content: space-around;
            /* background-color: yellow; */
        }

        table tr td {
            border: 2;
        }

        .status-ada {
            background-color: #90EE90;
            /* Hijau muda */
            text-align: center;
        }

        .status-tidak {
            background-color: #FFB6C1;
            /* Merah muda */
            text-align: center;
        }

        .hijau {
            background-color: green;
        }

        .merah {
            background-color: red;
        }
    </style>
</head>

<body>
    <div class="kepala">
        <div class="kepala-kiri">

            {{-- Simpan gambar di public --}}
            <img src="{{ asset('logo_pthas.jpg') }}" alt="Logo PTHAS" width="150">
            {{-- Jika untuk ekspor ke PDF, PDF tiidak bisa akses directory relatif public --}}
            {{-- <img src="{{ public_path('logo_pthas.jpg') }}" width="150" alt="Logo PTHAS"0> --}}

        </div>
        <div class="kepala-tengah">
            <h2>BAGI HASIL SEWA ALAT</h2>
            <h2>PERIODE {{ \Carbon\Carbon::parse($start_date)->format('j F Y') }} S.D
                {{ \Carbon\Carbon::parse($end_date)->format('j F Y') }}</h2>
            <h2>INVESTOR : {{ $record->nama }}</h2>
        </div>
        <div class="kepala-kanan"></div>
    </div>
    <table border="1" cellpadding="4" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead style="background-color: #c5d9f0;">
            <tr>
                <th style="width: 20%">TANGGAL</th>
                <th style="width: 40%">KETERANGAN</th>
                <th style="width: 15%">LAMA SEWA</th>
                <th style="width: 25%">PEMASUKAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    {{-- mengubah format date dari string jadi carbon lalu ubah format dari Y-m-d jadi j F Y --}}
                    <td>{{ \Carbon\Carbon::parse($item->tgl_keluar)->format('j F Y') }}</td>

                    <td> {{ Illuminate\Support\Str::title(optional($item->sewa->corporate)->nama) ?? Illuminate\Support\Str::title(optional($item->sewa->perorangan->first())->nama ?? 'HAS Survey') }}
                        sewa
                        {{ Illuminate\Support\Str::title($item->daftarAlat->jenisAlat->nama) }}
                        {{ $item->daftarAlat->nomor_seri }}
                    </td>
                    <td>{{ round(\Carbon\Carbon::parse($item->tgl_masuk)->diffInDays(\Carbon\Carbon::parse($item->tgl_keluar), true)) + 1 }}
                        hari
                        @if ($item->tgl_masuk == null)
                            (belum selesai)
                        @endif
                    </td>
                    {{-- argumen 1: variabel/data; arg 2: jenis currency; arg 3 local bisa en bisa id, dll.;arg 4:precision 0 koma --}}
                    <td style="text-align: right"> <span style="float: left;">Rp</span>
                        {{ number_format($item->biaya_sewa_alat_final, 0, ',', ',') }}
                    </td>
                    {{-- bisa juga pake yang bawah --}}
                    {{-- <td >{{ Illuminate\Support\Number::currency($item->biaya_sewa_alat_final, 'IDR', 'id', 0) }}</td> --}}

                </tr>
            @endforeach
            {{-- menambahkan satu baris baru setelah melooping semua data --}}
            <tr style="background-color: #c5d9f0;">
                <th colspan="3">Total Pemasukan</th>
                {{-- pake $items untuk menjumlahkan semuanya bukan $item, karena $item hanya satu --}}
                <th style="text-align: right">
                    <span style="float: left;">Rp</span>
                    {{ number_format($items->sum('biaya_sewa_alat_final'), 0, ',', ',') }}

            </tr>
        </tbody>
    </table>
    <br>
    <table border="2" cellpadding="4" cellspacing="0" style="border-collapse: collapse; width: 80%;">
        <thead style="background-color: #c5d9f0;">
            <tr style="background-color: #ffc000">
                <th colspan="3">Bagi Hasil</th>
            </tr>
            <tr>
                <th rowspan="2">Total Pemasukan</th>
                <th>HAS</th>
                <th>Investor</th>
            </tr>
            <tr>
                <th>80%</th>
                <th>20%</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: right"><span style="float: left;">Rp</span>
                    {{ number_format($items->sum('biaya_sewa_alat_final'), 0, ',', ',') }}</td>
                <td style="text-align: right;background-color:#ddd9c4  "><span style="float: left;">Rp</span>
                    {{ number_format($items->sum('pendapatanhas_final'), 0, ',', ',') }}</td>
                <td style="text-align: right;background-color:#ddd9c4  "><span style="float: left;">Rp</span>
                    {{ number_format($items->sum('pendapataninv_final'), 0, ',', ',') }}</td>

                {{-- <td style="background-color:#ddd9c4 ">32432</td> --}}

            </tr>
        </tbody>
    </table>
    <br>
    <table border="2" cellpadding="4" cellspacing="0" style="border-collapse: collapse; width: 80%;">
        <thead style="background-color: #c5d9f0;">
            <tr style="background-color: #ffc000">
                <th>Yang harus disetor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: right;background-color:#ddd9c4  "><span style="float: left;">Rp</span>
                    {{ number_format($items->sum('pendapataninv_final'), 0, ',', ',') }}</td>
            </tr>
        </tbody>
    </table>

    @foreach ($alatData as $alat)
        <div class="page-break"></div>
        <div class="kalender">
            <h2 style="text-align: center">KALENDER SEWA</h2>
            <h2 style="text-align: center">PERIODE {{ \Carbon\Carbon::parse($start_date)->format('j F Y') }} S.D
                {{ \Carbon\Carbon::parse($end_date)->format('j F Y') }}</h2>
        </div>
        <div class="infoalat">
            <div class="infoalat-jenis">
                <h2>
                    {{ $alat['alat']->jenisAlat->nama }}
                </h2>
            </div>
            <div class="infoalat-kode">
                <h2>SERIAL NUMBER : </h2>
                <h2>
                    {{ $alat['alat']->nomor_seri }}
                </h2>
            </div>
        </div>

        <br>

        <table border="1" cellpadding="2" cellspacing="0"
            style="border-collapse: collapse; width: 100%; font-size:14px;">
            <thead style="background-color: #c5d9f0;">
                <tr>
                    <th style="width: 20%">TANGGAL</th>
                    <th style="width: 10%">ADA SEWA</th>
                    <th style="width: 10%">TIDAK ADA SEWA</th>
                    <th style="width: 20%">PENYEWA</th>
                    {{-- <th style="width: 20%">YANG SUDAH DIBAYAR</th> --}}
                    {{-- <th style="width: 20%">YANG BELUM DIBAYAR</th> --}}
                </tr>
            </thead>
            <tbody>
                {{-- @php
                    // Tanggal mulai: 28 bulan ini
                    $startDate = \Carbon\Carbon::now()->day(28)->startOfDay();

                    // Tanggal akhir: 27 bulan depan
                    $endDate = $startDate->copy()->addMonthNoOverflow()->day(27);

                    // Inisialisasi tanggal iterator
                    $currentDate = $startDate->copy();
                @endphp --}}

                @forelse ($alat['riwayat'] as $r)
                    <tr class="{{ $r['status'] == 'ada sewa' ? 'table-success' : 'table-danger' }}">
                        <td>{{ $r['tanggal'] }}</td>
                        @if ($r['status'] == 'ada sewa')
                            <td class="text-capitalize hijau"></td>
                        @else
                            <td class="text-capitalize"></td>
                        @endif

                        @if ($r['status_invers'] == 'merah')
                            <td class="text-capitalize merah"></td>
                        @else
                            <td class="text-capitalize"></td>
                        @endif
                        <td>
                            {{-- Tampilkan nama penyewa kalau ada, atau tanda "-" --}}
                            {{ $r['penyewa'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-muted fst-italic">Tidak ada data riwayat</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

</body>

</html>
