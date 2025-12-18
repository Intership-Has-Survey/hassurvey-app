<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Preview Penawaran</title>

    <style>
        /* untuk menetapkan ukuran */
        @page {
            size: A4;
            /* margin: 20mm; */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header img {
            width: 120px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 3px solid #22AA44;
            padding-bottom: 10px;
            /* margin-bottom: 10px; */
            align-items: center;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
            text-align: right;
        }

        .info-invoice {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .info-invoice * {
            flex: 1;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1px;
        }

        .table th,
        .table td {
            border: 1px solid black;
            padding: 8px;
            vertical-align: top;
        }

        .table th {
            background: #0a9239;
            color: white;
            text-align: center;
        }

        .section-title {
            font-weight: bold;
            background: #e6e6e6;
        }

        .terbilang {
            background: #009e2f;
            color: white;
            padding: 10px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }

        .totals {
            margin-top: 10px;
            width: 40%;
            float: right;
            border-collapse: collapse;
        }

        .totals td {
            padding: 6px;
            border: 1px solid black;
        }

        .totals .green {
            background: #009e2f;
            color: white;
            font-weight: bold;
        }

        .bank-info {
            margin-top: 30px;
        }

        .signature {
            margin-top: 60px;
            text-align: right;
        }

        .signature img {
            width: 150px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div style="flex:1; align-items: center;">
            <img src="{{ asset('logo_pthas.jpg') }}" alt="Logo PTHAS" style="flex: 1;">

        </div>
        {{-- <img src="/path/logo.png"> --}}
        <div style= "flex:2; padding-right:40px;">
            <strong>{{ $penawaranSetting->nama_perusahaan }}</strong><br>
            {{ $penawaranSetting->alamat }}<br>
            Phone: {{ $penawaranSetting->phone }}, Mobile: {{ $penawaranSetting->mobile }}<br>
            e-mail: {{ $penawaranSetting->email }}<br>
            {{-- <strong>PT. HAS SURVEY GEOSPASIAL INDONESIA</strong><br>
            Jl. Bakau Blok B No 1 RT.01/RW.05 Kel. Sukadamai
            Kecamatan Tanah Sareal Kota Bogor Provinsi Jawa Barat<br>
            Phone: 0251-8423039, Mobile: 0821-2441-1160<br>
            e-mail: corporate@has-surveying.com<br> --}}
            web: https://www.has-surveying.com
        </div>
        <div class="invoice-title" style="flex:1;">PENAWARAN</div>
    </div>

    {{-- INFORMASI INVOICE --}}
    <div class="info-invoice">
        <div class="tujuan">
            <strong>Kepada :</strong><br>
            CONTOH KLIEN <br>
            Contoh Alamat 1213<br>
            <br>
        </div>
        <div class="nomor">
            <table>
                <tr>
                    <td><strong>Nomor Invoice</strong></td>
                    {{-- <td>: {{ $invoice->kode_invoice }}</td> --}}
                    <td>: QTN-M-XI-29-001</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Invoice</strong></td>
                    <td>: 17 Desember 2026</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- TABEL ITEM --}}
    <table class="table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Deskripsi</th>
                <th style="width: 60px;">Satuan</th>
                <th style="width: 40px;">Qty</th>
                <th style="width: 130px;">Harga Satuan</th>
                <th style="width: 130px;">Harga Total</th>
            </tr>
        </thead>
        <tbody>
            {{-- @foreach ($penawaran->detailPenawarans as $i => $row) --}}
            <tr>
                <td style="text-align:center; vertical-align:top;">1</td>
                <td><strong>Pengukuran</strong><br>
                    Rincian <br>
                    - Kegiatan Pembangunan Perumahan di Desa Sukamaju,<br>
                    - Kecamatan Sukamakmur, Kabupaten Bogor, Jawa Barat <br>
                </td>
                {{-- <td>{!! nl2br(\App\Helpers\StringHelper::htmlToTextWithNewlines($row->nama)) !!}</td> --}}
                <td style="text-align:center;">Unit</td>

                <td style="text-align:center;">2</td>

                <td> Rp 25.000,00 </td>
                <td> Rp 50.000,00 </td>
            </tr>
            {{-- @endforeach --}}
            <tr style="text-align: left">
                <td colspan="4" style="border: none"></td>
                <th class="grand-total" style="text-align: left">Grand Total</th>
                <th class="grand-total" style="text-align: left">Rp 50.000,00</th>
            </tr>
        </tbody>
    </table>

    <div style="clear:both;"></div>

    {{-- BANK TRANSFER INFO --}}
    <div class="bank-info">
        <strong>Pembayaran melalui Transfer Bank:</strong><br>
        Nama Pemilik Rekening : HAS SURVEY GEOSPASIAL INDONESIA <br>
        Nomor Rekening : 8721427811 <br>
        Nama Bank : BANK CENTRAL ASIA (BCA) <br><br>

        <strong>Catatan:</strong><br>
        - Invoice ini berlaku sebagai bukti penagihan <br>
        - Harap konfirmasi setelah melakukan pembayaran <br>
    </div>

    {{-- SIGNATURE --}}
    <div class="signature">
        Hormat kami,
        <br><br><br>
        <br><br><br>
        <br>
        <strong>Ahmad Fauji Rifai, S.T</strong><br>
        Direktur Utama
    </div>

</body>

</html>
