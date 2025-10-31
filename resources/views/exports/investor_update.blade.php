<!DOCTYPE html>
<html>

<head>
    {{-- CSS tetap sama --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <title>Generate Laporan Investor</title>
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

        .kepala-kanan,
        .kepala-kiri {
            flex: 1;
        }

        .kepala-tengah {
            text-align: center;
            line-height: 100%;
            flex: 3;
            font-size: 10px;
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
        }

        table tr td {
            border: 2;
            font-size: 12px;
        }

        .status-ada {
            background-color: #90EE90;
            text-align: center;
        }

        .status-tidak {
            background-color: #FFB6C1;
            text-align: center;
        }

        .hijau {
            background-color: green;
        }

        .merah {
            background-color: red;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    {{-- Halaman pertama (tetap sama) --}}
    <div class="kepala">
        <div class="kepala-kiri">
            <img src="{{ asset('logo_pthas.jpg') }}" alt="Logo PTHAS" width="150">
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
                    <td>{{ \Carbon\Carbon::parse($item->tgl_keluar)->format('j F Y') }}</td>
                    <td>
                        {{ Illuminate\Support\Str::title(
                            $item->sewa?->corporate?->nama ?? ($item->sewa?->perorangan?->first()?->nama ?? 'HAS'),
                        ) }}
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
                    <td style="text-align: right"> <span style="float: left;">Rp</span>
                        {{ number_format($item->sudah_dibayar, 0, ',', ',') }}
                    </td>
                </tr>
            @endforeach
            <tr style="background-color: #c5d9f0;">
                <th colspan="3">Total Pemasukan</th>
                <th style="text-align: right">
                    <span style="float: left;">Rp</span>
                    {{ number_format($items->sum('sudah_dibayar'), 0, ',', ',') }}
                </th>
            </tr>
        </tbody>
    </table>

    {{-- Bagian bagi hasil (tetap sama) --}}
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
                    {{ number_format($items->sum('sudah_dibayar'), 0, ',', ',') }}</td>
                <td style="text-align: right;background-color:#ddd9c4  "><span style="float: left;">Rp</span>
                    {{ number_format($items->sum('sudah_dibayar') * 0.8, 0, ',', ',') }}</td>
                <td style="text-align: right;background-color:#ddd9c4  "><span style="float: left;">Rp</span>
                    {{ number_format($items->sum('sudah_dibayar') * 0.2, 0, ',', ',') }}</td>
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
                    {{ number_format($items->sum('sudah_dibayar') * 0.2, 0, ',', ',') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Halaman detail alat (diperbaiki) --}}
    @foreach ($alatData as $alat)
        <div class="page-break"></div>
        <div class="kalender">
            <h2 style="text-align: center">KALENDER SEWA</h2>
            <h2 style="text-align: center">PERIODE {{ \Carbon\Carbon::parse($start_date)->format('j F Y') }} S.D
                {{ \Carbon\Carbon::parse($end_date)->format('j F Y') }}</h2>
        </div>
        <div class="infoalat">
            <div class="infoalat-jenis">
                <h2>{{ $alat['alat']->jenisAlat->nama }}</h2>
            </div>
            <div class="infoalat-kode">
                <h2>SERIAL NUMBER : </h2>
                <h2>{{ $alat['alat']->nomor_seri }}</h2>
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
                    <th style="width: 20%">SUDAH DIBAYAR</th>
                    <th style="width: 20%">BELUM DIBAYAR</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $currentPenyewa = null;
                    $groupStartIndex = 0;
                    $rowspanCount = 0;
                @endphp

                @foreach ($alat['riwayat'] as $index => $r)
                    @php
                        // Reset grouping jika penyewa berubah
                        if ($currentPenyewa !== $r['penyewa']) {
                            $currentPenyewa = $r['penyewa'];
                            $groupStartIndex = $index;

                            // Hitung berapa hari berturut-turut dengan penyewa yang sama
                            $rowspanCount = 1;
                            for ($i = $index + 1; $i < count($alat['riwayat']); $i++) {
                                if ($alat['riwayat'][$i]['penyewa'] === $currentPenyewa) {
                                    $rowspanCount++;
                                } else {
                                    break;
                                }
                            }
                        }
                    @endphp

                    <tr class="{{ $r['status'] == 'ada sewa' ? 'table-success' : 'table-danger' }}">
                        <td>{{ \Carbon\Carbon::Parse($r['tanggal'])->format('j F Y') }}</td>

                        @if ($r['status'] == 'ada sewa')
                            <td class="hijau"></td>
                        @else
                            <td></td>
                        @endif

                        @if ($r['status_invers'] == 'merah')
                            <td class="merah"></td>
                        @else
                            <td></td>
                        @endif

                        <td>{{ $r['penyewa'] }}</td>

                        {{-- Kolom SUDAH DIBAYAR dan BELUM DIBAYAR --}}
                        @if ($index === $groupStartIndex)
                            @if ($r['status'] == 'ada sewa')
                                <td rowspan="{{ $rowspanCount }}"
                                    style="vertical-align: middle; text-align: right; padding: 0 10px;font-size:12px;">
                                    {{-- @if ($r['sudah_dibayar'] > 0)
                                        <span style="float: left;">Rp</span>
                                        {{ number_format($r['sudah_dibayar'], 0, ',', '.') }}
                                        @else
                                        -
                                        @endif --}}
                                    <span style="float: left;">Rp</span>
                                    {{ number_format($r['sudah_dibayar'], 0, ',', '.') }}
                                </td>
                                <td rowspan="{{ $rowspanCount }}"
                                    style="vertical-align: middle; text-align: right; padding: 0 10px; font-size:12px;">
                                    @if ($r['sudah_dibayar'] >= 1 && $r['sudah_dibayar'] > $r['harga_final'])
                                        LUNAS
                                    @else
                                        <span style="float: left;">Rp</span>
                                        {{ number_format($r['harga_final'] - $r['sudah_dibayar'], 0, ',', '.') }}
                                    @endif
                                </td>
                            @else
                                <td rowspan="{{ $rowspanCount }}"
                                    style="vertical-align: middle; text-align: right; padding: 0 10px">
                                    -
                                </td>
                                <td rowspan="{{ $rowspanCount }}"
                                    style="vertical-align: middle; text-align: right; padding: 0 10px">
                                    -
                                </td>
                            @endif
                        @endif
                    </tr>
                @endforeach

                @if (count($alat['riwayat']) === 0)
                    <tr>
                        <td colspan="6" class="text-muted fst-italic">Tidak ada data riwayat</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach
</body>

</html>
