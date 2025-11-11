<!DOCTYPE html>
<html>

<head>
    <title>Pilih Periode Laporan Investor</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        select,
        input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            font-family: "Open Sans", sans-serif;
        }

        select:focus,
        input:focus {
            outline: none;
            border-color: #4A90E2;
        }

        .btn {
            background-color: #4A90E2;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #357ABD;
        }

        .info-box {
            background-color: #e8f4ff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Generate Laporan Investor</h1>

        <form action="{{ route('pdf.preview', ['company' => $company, 'investorId' => $investorId]) }}" method="GET">
            @csrf

            <div class="form-group">
                <label for="tahun">Tahun:</label>
                <select name="tahun" id="tahun" required>
                    <option value="">Pilih Tahun</option>
                    @for ($year = date('Y'); $year >= 2020; $year--)
                        <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="form-group">
                <label for="periode">Periode:</label>
                <select name="periode" id="periode" required>
                    <option value="">Pilih Periode</option>
                    <option value="1" {{ request('periode') == '1' ? 'selected' : '' }}>28 Januari - 27 Februari
                    </option>
                    <option value="2" {{ request('periode') == '2' ? 'selected' : '' }}>28 Februari - 27 Maret
                    </option>
                    <option value="3" {{ request('periode') == '3' ? 'selected' : '' }}>28 Maret - 27 April
                    </option>
                    <option value="4" {{ request('periode') == '4' ? 'selected' : '' }}>28 April - 27 Mei</option>
                    <option value="5" {{ request('periode') == '5' ? 'selected' : '' }}>28 Mei - 27 Juni</option>
                    <option value="6" {{ request('periode') == '6' ? 'selected' : '' }}>28 Juni - 27 Juli</option>
                    <option value="7" {{ request('periode') == '7' ? 'selected' : '' }}>28 Juli - 27 Agustus
                    </option>
                    <option value="8" {{ request('periode') == '8' ? 'selected' : '' }}>28 Agustus - 27 September
                    </option>
                    <option value="9" {{ request('periode') == '9' ? 'selected' : '' }}>28 September - 27 Oktober
                    </option>
                    <option value="10" {{ request('periode') == '10' ? 'selected' : '' }}>28 Oktober - 27 November
                    </option>
                    <option value="11" {{ request('periode') == '11' ? 'selected' : '' }}>28 November - 27 Desember
                    </option>
                    <option value="12" {{ request('periode') == '12' ? 'selected' : '' }}>28 Desember - 27 Januari
                    </option>
                </select>
            </div>

            <button type="submit" class="btn">Generate Laporan</button>
        </form>

        <div class="info-box">
            <strong>Informasi:</strong><br>
            Sistem periode menggunakan format 28 Bulan A sampai 27 Bulan B.
            Contoh: Periode 1 = 28 Januari sampai 27 Februari
        </div>
    </div>

    <script>
        // Auto-select tahun berjalan jika belum dipilih
        document.addEventListener('DOMContentLoaded', function() {
            const tahunSelect = document.getElementById('tahun');
            if (!tahunSelect.value) {
                tahunSelect.value = new Date().getFullYear();
            }
        });
    </script>
</body>

</html>
