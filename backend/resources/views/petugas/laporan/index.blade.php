@extends('layouts.app')

@section('title', 'Laporan Peminjaman - Dashboard Petugas')
@section('header-title', 'Laporan Peminjaman & Pengembalian Alat')

@section('content')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Filter Laporan</h3>
        </div>
        <form action="{{ route('petugas.laporan.index') }}" method="GET"
            class="p-5 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status Peminjaman:</label>
                <select name="status"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan
                    </option>
                    <option value="telat" {{ request('status') == 'telat' ? 'selected' : '' }}>Telat</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal (Pinjam):</label>
                <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal (Pinjam):</label>
                <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="flex space-x-2">
                <button type="submit"
                    class="flex-1 bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-lg transition shadow-sm">
                    Filter
                </button>
                <a href="{{ route('petugas.laporan.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg transition flex items-center justify-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Hasil & Tombol Cetak -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div
            class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:justify-between md:items-center gap-3">
            <h3 class="text-lg font-bold text-gray-800">Hasil Rekap Laporan</h3>
            <a href="{{ route('petugas.laporan.cetak', request()->all()) }}" target="_blank"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-semibold rounded-lg transition shadow-sm flex items-center space-x-2">
                <span>Cetak / Print Laporan</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">No</th>
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Detail Alat</th>
                        <th class="py-3 px-4 border-b">Denda</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($laporans as $index => $item)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $item->user->name ?? '-' }}</td>
                            <td class="py-3 px-4 border-b">{{ $item->tgl_pinjam }}</td>
                            <td class="py-3 px-4 border-b">{{ $item->tgl_kembali_plan }}</td>
                            <td class="py-3 px-4 border-b text-center">
                                <span
                                    class="inline-block px-2.5 py-1 rounded text-xs font-semibold
                                    {{ in_array($item->status, ['selesai', 'dikembalikan']) ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $item->status == 'dipinjam' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $item->status == 'telat' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $item->status == 'diajukan' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    @foreach ($item->detailPinjams as $detail)
                                        <li>{{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3 px-4 border-b font-semibold">
                                Rp {{ number_format($item->pengembalian->denda ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">Tidak ada data laporan yang sesuai
                                filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
