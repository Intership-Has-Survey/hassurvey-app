<div class="container my-4">
    <h2 class="mb-3 text-center">Riwayat Sewa Alat</h2>
    <h4 class="text-center text-muted mb-4">{{ $pemilik->nama }}</h4>
    <p class="text-center mb-5">
        <strong>Periode:</strong> {{ $start_date->format('d M Y') }} - {{ $end_date->format('d M Y') }}
    </p>

    @foreach ($alatData as $item)
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-primary text-white">
                <strong>{{ $item['alat']->nama_alat }}</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-bordered m-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%">Tanggal</th>
                            <th style="width: 30%">Penyewa</th>
                            <th style="width: 20%">Ada Sewa</th>
                            <th style="width: 20%">Tidak Ada Sewa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($item['riwayat'] as $r)
                            <tr class="{{ $r['status'] == 'ada sewa' ? 'table-success' : 'table-danger' }}">
                                <td>{{ $r['tanggal'] }}</td>
                                <td>
                                    {{-- Tampilkan nama penyewa kalau ada, atau tanda "-" --}}
                                    {{ $r['penyewa'] ?? '-' }}
                                </td>
                                <td class="text-capitalize">{{ $r['status'] }}</td>
                                <td class="text-capitalize">{{ $r['status_invers'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-muted fst-italic">Tidak ada data riwayat</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
