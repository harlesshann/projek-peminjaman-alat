<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Peminjaman</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .print-shadow { box-shadow: none !important; border: 1px solid #999 !important; }
        }
        @page { size: A4 landscape; margin: 12mm; }
    </style>
</head>
<body class="bg-gray-200 font-sans text-sm text-gray-800">
    <div class="no-print max-w-5xl mx-auto mt-4 flex flex-wrap gap-2 justify-between items-center bg-white p-3 rounded-lg shadow">
        <a href="{{ route('petugas.laporan.index', request()->only(['start_date', 'end_date', 'status'])) }}"
            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 text-xs font-semibold rounded-lg transition">
            &larr; Kembali ke Filter
        </a>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.print()"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-xs font-semibold rounded-lg transition">
                Print Sekarang
            </button>
            <a href="{{ route('petugas.laporan.cetak', array_merge(request()->only(['start_date', 'end_date', 'status']), ['output' => 'pdf'])) }}"
                target="_blank"
                class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-xs font-semibold rounded-lg transition">
                Buka PDF
            </a>
            <a href="{{ route('petugas.laporan.cetak', array_merge(request()->only(['start_date', 'end_date', 'status']), ['output' => 'pdf', 'download' => '1'])) }}"
                class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 text-xs font-semibold rounded-lg transition">
                Download PDF
            </a>
        </div>
    </div>

    <div class="max-w-5xl mx-auto my-4 bg-white p-8 rounded-lg shadow print-shadow">
        <div class="text-center border-b-4 border-double border-gray-800 pb-4 mb-4">
            <h1 class="text-xl font-bold uppercase tracking-wide">Sistem Peminjaman Alat</h1>
            <h2 class="text-base">Laporan Data Peminjaman</h2>
            <p class="text-xs text-gray-500">Jl. Pendidikan No. 1 &bull; Telp: (021) 000-0000 &bull; Email: info@peminjaman.test</p>
        </div>

        <div class="text-center mb-4">
            <h3 class="text-base font-bold underline uppercase">Laporan Peminjaman Alat</h3>
            <p class="text-xs text-gray-600 mt-1">
                @if (!empty($filter['start_date']) && !empty($filter['end_date']))
                    Periode: {{ \Carbon\Carbon::parse($filter['start_date'])->translatedFormat('d F Y') }}
                    s/d {{ \Carbon\Carbon::parse($filter['end_date'])->translatedFormat('d F Y') }}
                @else
                    Periode: Semua Data
                @endif
                &bull; Status: {{ !empty($filter['status']) ? ucfirst($filter['status']) : 'Semua' }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Dicetak oleh {{ $dicetak_oleh }} pada {{ $tanggal_cetak }}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mb-4 text-center text-xs">
            <div class="border rounded p-2"><p class="text-gray-500">Diajukan</p><p class="font-bold text-sm">{{ $summary['per_status']['diajukan'] }}</p></div>
            <div class="border rounded p-2"><p class="text-gray-500">Dipinjam</p><p class="font-bold text-sm">{{ $summary['per_status']['dipinjam'] }}</p></div>
            <div class="border rounded p-2"><p class="text-gray-500">Dikembalikan</p><p class="font-bold text-sm">{{ $summary['per_status']['dikembalikan'] }}</p></div>
            <div class="border rounded p-2"><p class="text-gray-500">Telat</p><p class="font-bold text-sm">{{ $summary['per_status']['telat'] }}</p></div>
            <div class="border rounded p-2"><p class="text-gray-500">Total Item</p><p class="font-bold text-sm">{{ $summary['total_item'] }} pcs</p></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-400 px-2 py-2">No</th>
                        <th class="border border-gray-400 px-2 py-2">Peminjam</th>
                        <th class="border border-gray-400 px-2 py-2">Alat Dipinjam</th>
                        <th class="border border-gray-400 px-2 py-2">Tgl Pinjam</th>
                        <th class="border border-gray-400 px-2 py-2">Rencana Kembali</th>
                        <th class="border border-gray-400 px-2 py-2">Tgl Kembali</th>
                        <th class="border border-gray-400 px-2 py-2">Status</th>
                        <th class="border border-gray-400 px-2 py-2">Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $i => $p)
                        <tr>
                            <td class="border border-gray-400 px-2 py-1.5 text-center">{{ $i + 1 }}</td>
                            <td class="border border-gray-400 px-2 py-1.5">{{ $p->user->name ?? 'User Dihapus' }}</td>
                            <td class="border border-gray-400 px-2 py-1.5">
                                <ul class="list-disc list-inside">
                                    @foreach ($p->detailPinjams as $d)
                                        <li>{{ $d->alat->nama_alat ?? 'Alat Dihapus' }} ({{ $d->jumlah }} pcs)</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="border border-gray-400 px-2 py-1.5 text-center whitespace-nowrap">{{ $p->tgl_pinjam?->format('d/m/Y') }}</td>
                            <td class="border border-gray-400 px-2 py-1.5 text-center whitespace-nowrap">{{ $p->tgl_kembali_plan?->format('d/m/Y') }}</td>
                            <td class="border border-gray-400 px-2 py-1.5 text-center whitespace-nowrap">{{ $p->pengembalian?->tgl_kembali?->format('d/m/Y') ?? '-' }}</td>
                            <td class="border border-gray-400 px-2 py-1.5 text-center">{{ ucfirst($p->status) }}</td>
                            <td class="border border-gray-400 px-2 py-1.5 text-right whitespace-nowrap">Rp {{ number_format((int) ($p->pengembalian->denda ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-gray-400 px-2 py-4 text-center text-gray-500">Tidak ada data pada periode / filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="text-xs mt-3">Total Transaksi: <strong>{{ $summary['total_transaksi'] }}</strong> &bull; Total Denda: <strong>Rp {{ number_format($summary['total_denda'], 0, ',', '.') }}</strong></p>

        <div class="flex justify-end mt-8 text-xs">
            <div class="text-center w-56">
                <p>{{ now()->translatedFormat('d F Y') }}</p>
                <p>Petugas,</p>
                <br><br><br>
                <p class="font-bold underline">{{ $dicetak_oleh }}</p>
            </div>
        </div>
    </div>

    @if (($auto_print ?? '0') === '1')
        <script>
            window.onload = function() { window.print(); };
        </script>
    @endif
</body>
</html>
