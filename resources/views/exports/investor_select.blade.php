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

        input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            font-family: "Open Sans", sans-serif;
        }

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

        .date-range-container {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .date-range-container .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .date-separator {
            font-weight: bold;
            color: #666;
        }

        @media (max-width: 480px) {
            .date-range-container {
                flex-direction: column;
                gap: 10px;
            }

            .date-separator {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Generate Laporan Investor</h1>

        <form action="{{ route('pdf.preview', ['company' => $company, 'investor' => $investor]) }}" method="GET">
            @csrf

            <div class="date-range-container">
                <div class="form-group">
                    <label for="start_date">Tanggal Mulai:</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                        required>
                </div>

                <div class="date-separator">s/d</div>

                <div class="form-group">
                    <label for="end_date">Tanggal Selesai:</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" required>
                </div>
            </div>

            <button type="submit" class="btn">Generate Laporan</button>
        </form>

        <div class="info-box">
            <strong>Informasi:</strong><br>
            Pilih rentang tanggal sesuai kebutuhan laporan. Pastikan tanggal selesai tidak lebih awal dari tanggal
            mulai.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            // Set default values jika belum ada (1 bulan terakhir)
            if (!startDateInput.value) {
                const endDate = new Date();
                const startDate = new Date();
                startDate.setDate(startDate.getDate() - 30);

                startDateInput.value = startDate.toISOString().split('T')[0];
                endDateInput.value = endDate.toISOString().split('T')[0];
            }

            // Validasi: end date tidak boleh sebelum start date
            startDateInput.addEventListener('change', function() {
                if (endDateInput.value && startDateInput.value > endDateInput.value) {
                    endDateInput.value = startDateInput.value;
                }
            });

            endDateInput.addEventListener('change', function() {
                if (startDateInput.value && endDateInput.value < startDateInput.value) {
                    startDateInput.value = endDateInput.value;
                }
            });
        });
    </script>
</body>

</html>
