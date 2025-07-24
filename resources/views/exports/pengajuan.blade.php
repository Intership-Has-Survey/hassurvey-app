<!DOCTYPE html>
<html>

<head>
    <title>Pengajuan PDF</title>
</head>

<body>
    <h1>Pengajuan</h1>
    <p>Judul Pengajuan: {{ $record->judul_pengajuan }}</p>
    <p>Deskripsi: {{ $record->deskripsi_pengajuan }}</p>
    <p>Bank: {{ $record->bank->nama_bank }}</p>
    <p>Total Biaya: {{ $record->nilai }}</p>

    <h3>Rincian Pengajuan</h3>

    <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead style="background-color: #f0f0f0;">
            <tr>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($record->detailPengajuans as $item)
                <tr>
                    <td>{{ $item->deskripsi }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
