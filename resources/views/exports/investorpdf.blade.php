<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan PDF</title>
    <style>
        /* Reset dan styling dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #eee;

        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
            background-color: #fff;
            padding: 3cm 3cm 3cm 4cm;
            /* Margin: atas, kanan, bawah, kiri */
            width: 21cm;
            /* Lebar A4 */
            height: 29.7cm;
            /* Tinggi A4 */
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 16pt;
            margin-bottom: 5px;
        }

        /* Informasi pengajuan */
        .info-pengajuan {
            margin-bottom: 20px;
        }

        .info-pengajuan p {
            margin-bottom: 5px;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        /* Halaman baru */
        .page-break {
            page-break-before: always;
            margin-top: 30px;
        }

        /* Detail alat */
        .detail-alat {
            margin-bottom: 20px;
        }

        .detail-alat h2 {
            font-size: 14pt;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        /* Riwayat sewa */
        .riwayat-sewa h3 {
            font-size: 12pt;
            margin-bottom: 10px;
        }

        /* Footer untuk nomor halaman */
        .page-number {
            position: fixed;
            bottom: 2cm;
            right: 3cm;
            font-size: 10pt;
        }
    </style>
</head>

<body>
    <!-- Halaman 1: Pengajuan dan Rincian -->
    <div class="header">
        <h1>PENGAJUAN</h1>
    </div>

    <div class="info-pengajuan">
        <p><strong>Judul Pengajuan:</strong> HILAL</p>
        <p><strong>Deskripsi:</strong> Pria</p>
        <p><strong>NIK:</strong> 3201168788350002</p>
    </div>

    <h2>Rincian Pengajuan</h2>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Lama Sewa</th>
                <th>Pemasukan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>23 September 2025</td>
                <td>ZSO04450, SOKKIA M4.52, Total Station</td>
                <td>1</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>24 September 2025</td>
                <td>ZSO04450, SOKKIA M4.52, Total Station</td>
                <td>1</td>
                <td>150,000.00</td>
            </tr>
            <tr>
                <td>25 September 2025</td>
                <td>ZSO04450, SOKKIA M4.52, Total Station</td>
                <td>1</td>
                <td>250,000.00</td>
            </tr>
            <tr>
                <td>2 Oktober 2025</td>
                <td>ZSO04450, SOKKIA M4.52, Total Station</td>
                <td>1</td>
                <td>250,000.00</td>
            </tr>
            <tr>
                <td>4 Oktober 2025</td>
                <td>ZSO04450, SOKKIA M4.52, Total Station</td>
                <td>1</td>
                <td>250,000.00</td>
            </tr>
            <tr>
                <td>11 September 2025</td>
                <td>12017465, SOKKIA M4.50, Total Station</td>
                <td>45.45</td>
                <td>0.00</td>
            </tr>
        </tbody>
    </table>

    <!-- Halaman 2: Detail Alat 1 -->
    <div class="page-break">
        <div class="detail-alat">
            <h2>Detail Alat</h2>
            <table>
                <tr>
                    <th width="30%">Nomor Seri</th>
                    <td>ZSO04450</td>
                </tr>
                <tr>
                    <th>Merk</th>
                    <td>SOKKIA M4.52</td>
                </tr>
                <tr>
                    <th>Jenis Alat</th>
                    <td>Total Station</td>
                </tr>
                <tr>
                    <th>Kondisi</th>
                    <td>Baik</td>
                </tr>
                <tr>
                    <th>Tanggal Masuk</th>
                    <td>23 September 2025</td>
                </tr>
            </table>
        </div>

        <div class="riwayat-sewa">
            <h3>Riwayat Sewa Alat Ini</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Keluar</th>
                        <th>Tanggal Masuk</th>
                        <th>Lama Sewa</th>
                        <th>Biaya Sewa</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>25 Oktober 2025</td>
                        <td>25 Oktober 2025</td>
                        <td>1</td>
                        <td>0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Halaman 3: Detail Alat 2 -->
    <div class="page-break">
        <div class="detail-alat">
            <h2>Detail Alat</h2>
            <table>
                <tr>
                    <th width="30%">Nomor Seri</th>
                    <td>12017465</td>
                </tr>
                <tr>
                    <th>Merk</th>
                    <td>SOKKIA M4.50</td>
                </tr>
                <tr>
                    <th>Jenis Alat</th>
                    <td>Total Station</td>
                </tr>
                <tr>
                    <th>Kondisi</th>
                    <td>Baik</td>
                </tr>
                <tr>
                    <th>Tanggal Masuk</th>
                    <td>11 September 2025</td>
                </tr>
            </table>
        </div>

        <div class="riwayat-sewa">
            <h3>Riwayat Sewa Alat Ini</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Keluar</th>
                        <th>Tanggal Masuk</th>
                        <th>Lama Sewa</th>
                        <th>Biaya Sewa</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="text-align: center;">Tidak ada riwayat</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="page-number">Halaman 1</div>
</body>

</html>
