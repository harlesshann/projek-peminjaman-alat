@extends('layouts.app')

@section('title', 'Cetak Laporan - Panel Admin')
@section('header-title', 'Cetak Laporan Peminjaman')

@section('content')
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-5">
        <h3 class="text-lg font-bold text-gray-800">Filter Laporan</h3>
        <p class="text-xs text-gray-500 mt-1">Saring berdasarkan periode tanggal pinjam dan status, lalu cetak / unduh PDF.</p>

        <form action="{{ route('admin.laporan.index') }}" method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Status --</option>
                    @foreach (['diajukan', 'dipinjam', 'dikembalikan', 'telat'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-lg transition">
                    Tampilkan
                </button>
                <a href="{{ route('admin.laporan.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 text-sm font-semibold rounded-lg transition">
                    Reset
                </a>
            </div>
        </form>
        @error('end_date')
            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Transaksi</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $summary['total_transaksi'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Item Dipinjam</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $summary['total_item'] }} pcs</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Denda</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($summary['total_denda'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col lg:flex-row justify-between gap-3">
            <h3 class="text-sm font-bold text-gray-800 self-center">
                Preview Data ({{ $peminjamans->total() }} data)
            </h3>
            @php $qs = request()->only(['start_date', 'end_date', 'status']); @endphp
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.laporan.cetak', array_merge($qs, ['output' => 'html'])) }}" target="_blank"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 text-xs font-semibold rounded-lg transition">
                    Preview Print
                </a>
                <a href="{{ route('admin.laporan.cetak', array_merge($qs, ['output' => 'html', 'auto_print' => '1'])) }}"
                    target="_blank"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 text-xs font-semibold rounded-lg transition">
                    Print Langsung
                </a>
                <a href="{{ route('admin.laporan.cetak', array_merge($qs, ['output' => 'pdf'])) }}" target="_blank"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-3 py-2 text-xs font-semibold rounded-lg transition">
                    Buka PDF
                </a>
                <a href="{{ route('admin.laporan.cetak', array_merge($qs, ['output' => 'pdf', 'download' => '1'])) }}"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-2 text-xs font-semibold rounded-lg transition">
                    Download PDF
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Denda</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjamans as $p)
                        <tr class="hover:bg-gray-50 align-top">
                            <td class="py-3 px-4 border-b font-medium">{{ $p->user->name ?? 'User Dihapus' }}</td>
                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside text-xs space-y-1">
                                    @foreach ($p->detailPinjams as $d)
                                        <li>{{ $d->alat->nama_alat ?? 'Alat Dihapus' }} ({{ $d->jumlah }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3 px-4 border-b text-xs whitespace-nowrap">
                                {{ $p->tgl_pinjam?->format('d M Y') }}<br>
                                <span class="text-gray-500">Rencana: {{ $p->tgl_kembali_plan?->format('d M Y') }}</span>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b text-xs">
                                Rp {{ number_format((int) ($p->pengembalian->denda ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500 text-sm">Tidak ada data sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $peminjamans->links() }}
        </div>
    </div>
@endsection
