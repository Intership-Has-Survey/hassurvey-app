<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Berita Acara Pekerjaan Selesai (BAPS)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        :root {
            --border: #444;
            --muted: #666;
            --gap: 12px;
        }

        body {
            font-family: "Times New Roman", serif;
            color: #111;
            margin: 36px;
            line-height: 1.55;
            font-size: 14px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0;
        }

        .logo img {
            width: 150px;
        }

        .company {
            text-align: right;
            font-size: 13px;
        }

        h1 {
            text-align: center;
            font-size: 20px;
            margin: 6px 0 4px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .subtitle {
            text-align: center;
            font-size: 13px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        th,
        td {
            padding: 8px 10px;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            width: 230px;
            font-weight: 600;
            text-align: left;
        }

        .section-title {
            font-weight: 700;
            margin: 20px 0 8px;
            font-size: 15px;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 36px;
        }

        .sig {
            width: 48%;
            text-align: center;
            font-size: 14px;
        }

        .sig .name {
            margin-top: 70px;
            font-weight: 700;
            text-decoration: underline;
        }

        .sig .role {
            font-size: 13px;
            color: var(--muted);
        }

        .foot {
            font-size: 12px;
            color: var(--muted);
            margin-top: 18px;
        }

        .header-divider {
            width: 100%;
            height: 2px;
            background: #333;
            /* margin: 0 0 10px; */
        }

        @media print {
            body {
                margin: 12mm;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- HEADER -->
        <header>
            <div class="logo">
                <img src="{{ asset('logo_pthas.jpg') }}" alt="Logo PTHAS">
            </div>
            <div class="company">
                <strong>PT. HAS SURVEY GEO SPASIAL</strong><br />
                Jl. Bakau Blok B No 1-2, RT.001/RW.005, Sukadamai, Tanah Sereal,<br />
                Kota Bogor, Jawa Barat 16164 <br />
                Tel: +62 821-2441-1160 • Email: corporate@has-surveying.com
            </div>
        </header>

        <div class="header-divider"></div>

        <!-- TITLE -->
        <h1>BERITA ACARA PEKERJAAN SELESAI</h1>
        <div class="subtitle">
            Nomor: {{ $nomor ?? '____/BAPS/____/' . now()->format('m') . '/' . now()->format('Y') }}
        </div>

        <!-- DESCRIPTION -->
        <p>Pada hari ini,
            <strong>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong>
            {{-- bertempat di kantor {{ $project->corporate?->nama }}, --}}
            telah dilaksanakan pemeriksaan hasil pekerjaan oleh Pihak Pertama dan Pihak Kedua.berikut disampaikan
            rincian pekerjaan yang menjadi bagian dari Berita Acara ini:
        </p>


        <table>
            <tr>
                <th>Nama Proyek</th>
                <td>: {{ $project->nama_project }}</td>
            </tr>
            <tr>
                <th>Pemberi Tugas (Pihak Pertama)</th>
                <td>: {{ $project->corporate?->nama ?? $project->perorangan->first()->nama }}</td>
            </tr>
            <tr>
                <th>Penyedia Jasa (Pihak Kedua)</th>
                <td>: PT. HAS Survey Geo Spasial</td>
            </tr>
            {{-- <tr>
                <th>Nomor Kontrak / SPK</th>
                <td>: {{ $project->nomor_kontrak ?? '_______________' }}</td>
            </tr> --}}
            <tr>
                <th>Tanggal Mulai Pekerjaan</th>
                <td>: {{ \Carbon\Carbon::parse($project->mulai)->translatedFormat('d, F Y') }}
                </td>
            </tr>
            <tr>
                <th>Tanggal Selesai Pekerjaan</th>
                <td>: {{ \Carbon\Carbon::parse($project->akhir)->translatedFormat('d, F Y') }}</td>
            </tr>
        </table>

        {{-- <p>{{ $project->ruang_lingkup ?? 'Pekerjaan dilaksanakan sesuai ruang lingkup yang tercantum dalam kontrak/SPK.' }}
        </p> --}}

        <p>Berdasarkan hasil pemeriksaan bersama yang dilakukan oleh Pihak Pertama dan Pihak Kedua, dapat disimpulkan
            bahwa seluruh rangkaian pekerjaan telah diselesaikan oleh Pihak Kedua secara menyeluruh dan memenuhi
            ketentuan kontrak. Pihak Pertama menyatakan bahwa pekerjaan telah:</p>
        <ul>
            <li>Diselesaikan 100% sesuai spesifikasi teknis,</li>
            <li>Memenuhi standar mutu pekerjaan,</li>
            <li>Dapat diterima tanpa catatan / dengan catatan seperlunya (jika ada).</li>
        </ul>

        <p>Dengan demikian, pekerjaan dinyatakan selesai dan dapat diserahterimakan dari Pihak Kedua kepada Pihak
            Pertama. Berita Acara ini dibuat untuk
            digunakan sebagaimana mestinya sebagai dokumen resmi penyelesaian pekerjaan.</p>

        <!-- SIGNATURES -->
        <div class="signatures">
            <div class="sig">
                <div><strong>Pihak Pertama</strong></div>
                <div class="role">{{ $project->corporate?->nama ?? $project->perorangan->first()->nama }}</div>
                <div class="name">{{ $project->perorangan?->first()->nama }}</div>
            </div>

            <div class="sig">
                <div><strong>Pihak Kedua</strong></div>
                <div class="role">PT. HAS Survey Geo Spasial</div>
                <div class="name">{{ $project->picInternal?->nama ?? '__________' }}</div>
            </div>
        </div>

        <div class="foot">
            Dokumen ini sah setelah ditandatangani oleh para pihak dan disimpan sebagai arsip administrasi proyek.
        </div>

    </div>

    {{-- <a href="{{ route('acara.download', [$project->company_id, $project->id]) }}" class="btn btn-primary">
        Download Berita Acara
    </a> --}}
</body>

</html>
