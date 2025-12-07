<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>

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
            background-color: #ccc;
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
            margin-top: 15px;
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
            <strong>PT. HAS SURVEY GEOSPASIAL INDONESIA</strong><br>
            Jl. Bakau Blok B No 1 RT.01/RW.05 Kel. Sukadamai
            Kecamatan Tanah Sareal Kota Bogor Provinsi Jawa Barat<br>
            Phone: 0251-8423039, Mobile: 0821-2441-1160<br>
            e-mail: corporate@has-surveying.com<br>
            web: https://www.has-surveying.com
        </div>
        <div class="invoice-title" style="flex:1;">INVOICE</div>
    </div>

    {{-- INFORMASI INVOICE --}}
    <div class="info-invoice">
        <div class="tujuan">
            <strong>Kepada :</strong><br>
            PT. Menara Selular Nusantara<br>
            Graha Aruna lantai 2, Jalan Antara No. 47 Kel. Pasar Baru, Sawah Besar, Jakarta Pusat
        </div>
        <div class="nomor">
            <table>
                <tr>
                    <td><strong>Nomor Invoice</strong></td>
                    <td>: 092/HAS-P/MSN/XI/2025</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Invoice</strong></td>
                    <td>: 27 November 2025</td>
                </tr>
                <tr>
                    <td><strong>Nomor PO</strong></td>
                    <td>: 021/PM/MSN/11.2025</td>
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
            @foreach ($invoice->detailInvoices as $i => $row)
                <tr>
                    <td style="text-align:center; vertical-align:top;">{{ $i + 1 }}</td>

                    <td>{!! nl2br(\App\Helpers\StringHelper::htmlToTextWithNewlines($row->nama)) !!}</td>
                    <td style="text-align:center;">
                        {{ $row->satuan }}
                    </td>

                    <td style="text-align:center;">
                        {{ $row->jumlah }}
                    </td>

                    <td>
                        Rp {{ number_format($row->harga, 0, ',', '.') }}
                    </td>

                    <td>
                        Rp {{ number_format($row->harga * $row->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TERBILANG --}}

    @php
        $subtotal = $invoice->detailInvoices->sum(function ($i) {
            return $i->harga * $i->jumlah;
        });

        $dp = $subtotal / 2;
        $pelunasan = $subtotal - $dp;
    @endphp

    {{-- TOTAL --}}
    <table class="totals">
        <tr>
            <td>Sub Total</td>
            <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
        </tr>

        <tr>
            <td>Term I DP 50%</td>
            <td>Rp {{ number_format($dp, 0, ',', '.') }}</td>
        </tr>

        <tr>
            <td class="green">Pelunasan</td>
            <td class="green">Rp {{ number_format($pelunasan, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div style="clear:both;"></div>

    {{-- BANK TRANSFER INFO --}}
    <div class="bank-info">
        <strong>Pembayaran melalui Transfer Bank:</strong><br><br>
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
