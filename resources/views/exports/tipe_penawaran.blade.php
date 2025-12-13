<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penawaran</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .header {
            text-align: left;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            width: 120px;
            float: left;
            margin-right: 15px;
        }

        .header .title {
            font-size: 26px;
            font-weight: bold;
            color: #0b8c25;
            text-align: right;
        }

        .info-table {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 3px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th, .table td {
            border: 1px solid black;
            padding: 8px;
            vertical-align: top;
        }

        .table th {
            background: #009e2f;
            color: white;
            text-align: center;
        }

        .grand-total {
            background: #009e2f;
            color: white;
            font-weight: bold;
        }

        .notes {
            margin-top: 15px;
            font-size: 13px;
        }

        .signature {
            margin-top: 40px;
            text-align: left;
        }

        .signature img {
            width: 140px;
        }

    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <img src="/path/logo.png" alt="Logo"> 
        <div>
            <strong>PT. HAS SURVEY GEOSPASIAL INDONESIA</strong><br>
            Jl. Bakau Blok B No 1 RT.01/RW.05 Kel. Sukadamai<br>
            Kecamatan Tanah Sareal Kota Bogor Provinsi Jawa Barat<br>
            Phone: 0251-8423039, Mobile: 081221535292<br>
            e-mail : corporate@has-surveying.com
        </div>
        <div class="title">PENAWARAN</div>
        <div style="clear: both"></div>
    </div>

    {{-- INFORMASI PENERIMA --}}
    <table class="info-table">
        <tr>
            <td><strong>Kepada</strong></td>
            <td>: Marcell</td>
            <td><strong>Nomor</strong></td>
            <td>: HSGI-QTN-M-XI-29-001</td>
        </tr>
        <tr>
            <td><strong>Alamat</strong></td>
            <td>: </td>
            <td><strong>Tanggal</strong></td>
            <td>: 29 November 2025</td>
        </tr>
    </table>

    {{-- TABEL PENAWARAN --}}
    <table class="table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Deskripsi</th>
                <th style="width: 60px;">Satuan</th>
                <th style="width: 40px;">Qty</th>
                <th style="width: 130px;">Harga Satuan</th>
                <th style="width: 130px;">Harga Total</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td>1</td>
                <td>
                    <strong>Pengukuran Topografi Gianyar</strong><br>
                    Rincian : <br>
                    - Peta Batas dan Situasi<br>
                    - Peta Topografi<br>
                    - Gambar Potongan<br>
                    - Gambar Orthophoto (Drone)
                </td>
                <td>Ls</td>
                <td>1</td>
                <td>Rp 4,500,000</td>
                <td>Rp 4,500,000</td>
            </tr>

            <tr>
                <td>2</td>
                <td><strong>Biaya Akomodasi dan Transportasi</strong></td>
                <td>Ls</td>
                <td>1</td>
                <td>Rp 3,500,000</td>
                <td>Rp 3,500,000</td>
            </tr>

            <tr>
                <td colspan="5" class="grand-total" style="text-align:right;">Grand Total</td>
                <td class="grand-total">Rp 8,000,000</td>
            </tr>

        </tbody>
    </table>

    {{-- CATATAN --}}
    <div class="notes">
        <strong>Catatan :</strong><br>
        - Harga belum termasuk PPN 11% <br>
        - Pembayaran Termin 1 sebesar 50% (<i>Down Payment</i>) <br>
        - Pembayaran Termin 2 sebesar 50% ketika pekerjaan selesai. <br>
        - Pembayaran melalui rekening BCA. 8721427811 an. HAS SURVEY GEOSPASIAL INDONESIA <br><br>
        Demikian informasi harga ini kami sampaikan dan atas perhatian dan kerjasamanya kami ucapkan terima kasih.
    </div>

    {{-- TANDA TANGAN --}}
    <div class="signature">
        Hormat Kami,<br><br>
        <img src="/path/ttd.png" alt="Tanda Tangan"><br>
        <strong>Ahmad Fauji Rifai</strong><br>
        Direktur Utama
    </div>

</body>
</html>
