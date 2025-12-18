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
            <strong>{{ $invoiceSetting->nama_perusahaan ?? 'PT. HAS SURVEY GEOSPASIAL INDONESIA' }}</strong><br>
            {{ $invoiceSetting->alamat ??
                'Jl. Bakau Blok B No 1 RT.01/RW.05 Kel. Sukadamai Kecamatan Tanah Sareal Kota Bogor Provinsi Jawa Barat' }}
            <br>
            Phone: {{ $invoiceSetting->telepon ?? ' 0251-8423039' }},
            Mobile: {{ $invoiceSetting->mobile ?? ' 0821-2441-1160' }}<br>
            Email: {{ $invoiceSetting->email ?? ' corporate@has-surveying.com' }} <br>
            web: https://www.has-surveying.com
        </div>
        <div class="invoice-title" style="flex:1;">INVOICE</div>
    </div>

    {{-- INFORMASI INVOICE --}}
    <div class="info-invoice">
        <div class="tujuan">
            <strong>Kepada :</strong><br>
            {{ $invoice->invoiceable->corporate?->nama ?? $invoice->invoiceable->perorangan?->first()->nama }}<br>
            {{ $invoice->invoiceable->corporate?->detail_alamat ?? $invoice->invoiceable->perorangan?->first()->detail_alamat }}<br>
            <br>
        </div>
        <div class="nomor">
            <table>
                <tr>
                    <td><strong>Nomor Invoice</strong></td>
                    <td>: {{ $invoice->kode_invoice }}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Invoice</strong></td>
                    <td>:{{ \Carbon\Carbon::parse($invoice->tanggal_mulai)->format('d F Y') }}</td>
                </tr>
                {{-- <tr>
                    <td><strong>Nomor PO</strong></td>
                    <td>: 021/PM/MSN/11.2025</td>
                </tr> --}}
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
            <td style="background-color: #d9ede1">Sub Total</td>
            <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
        </tr>

        <tr>
            <td style="background-color: #d9ede1">{{ $invoice->jenis }} ({{ $invoice->jumlah_pembayaran }}%)</td>
            <td>Rp {{ number_format($subtotal * ($invoice->jumlah_pembayaran / 100), 0, ',', '.') }}</td>
        </tr>

        <tr>
            <td style="background-color: #d9ede1">PPN</td>
            <td>
                {{ $invoice->ppn == 0 ? 'Tidak ada' : $invoice->ppn . '%' }}
            </td>
        </tr>

        <tr>
            <td class="green">Total Tagihan</td>
            <td class="green">Rp
                {{ number_format($subtotal * (1 + $invoice->ppn / 100) * ($invoice->jumlah_pembayaran / 100), 0, ',', '.') }}
                {{-- {{ number_format($subtotal - $subtotal * ($invoice->jumlah_pembayaran / 100), 0, ',', '.') }}</td> --}}
        </tr>
    </table>

    {{-- baris baru --}}

    <div style="clear:both;"></div>

    {{-- BANK TRANSFER INFO --}}
    <div class="bank-info">
        <strong>Pembayaran melalui Transfer Bank:</strong><br>
        @if (!empty($invoiceSetting->penutup))
            {!! nl2br(\App\Helpers\StringHelper::htmlToTextWithNewlines($invoiceSetting->penutup)) !!}
        @else
            Nama Pemilik Rekening : HAS SURVEY GEOSPASIAL INDONESIA <br>
            Nomor Rekening : 8721427811 <br>
            Nama Bank : BANK CENTRAL ASIA (BCA) <br>
        @endif
        <br>
        <br>

        <strong>Catatan:</strong><br>
        @if (!empty($invoiceSetting->catatan))
            {!! nl2br(\App\Helpers\StringHelper::htmlToTextWithNewlines($invoiceSetting->catatan)) !!}
        @else
            - Invoice ini berlaku sebagai bukti penagihan <br>
            - Harap konfirmasi setelah melakukan pembayaran <br>
        @endif
    </div>

    {{-- SIGNATURE --}}
    <div class="signature">
        {{ $invoiceSetting->signature_name ?? 'Hormat Kami, ' }}
        <br><br><br>
        <br><br><br>
        <br>
        <strong>{{ $invoiceSetting->nama ?? 'Ahmad Fauji Rifai, S.T' }}</strong><br>
        {{ $invoiceSetting->jabatan ?? 'Direktur Utama' }}
    </div>

</body>

</html>
