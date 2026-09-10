@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Dashboard Petugas')
@section('header-title', 'Pemantauan Pengembalian Alat')

@section('content')
    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Daftar Peminjaman Aktif (Belum Kembali)</h3>
            </div>

            <form action="{{ route('petugas.pengembalian.index') }}" method="GET" class="flex w-full md:w-80 shrink-0">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                    Cari
                </button>
                @if (request('search'))
                    <a href="{{ route('petugas.pengembalian.index') }}"
                        class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b text-center">Status</th>
                        <th class="py-3 px-4 border-b">Detail Alat</th>
                        <th class="py-3 px-4 border-b text-center">Aksi Pengembalian</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjamans as $item)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                {{ $item->user->name ?? 'User Dihapus' }}
                            </td>
                            <td class="py-3 px-4 border-b whitespace-nowrap">{{ $item->tgl_pinjam }}</td>
                            <td class="py-3 px-4 border-b whitespace-nowrap">{{ $item->tgl_kembali_plan }}</td>
                            <td class="py-3 px-4 border-b text-center">
                                <span
                                    class="inline-block px-2.5 py-1 rounded text-xs font-semibold {{ $item->status == 'telat' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    @foreach ($item->detailPinjams as $detail)
                                        <li>
                                            <span
                                                class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            (Jumlah: {{ $detail->jumlah }})
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3 px-4 border-b text-center">
                                <form action="{{ route('petugas.pengembalian.proses', $item->id) }}" method="POST"
                                    class="inline-block text-left mt-0 p-3 bg-gray-50 border border-gray-200 rounded-lg space-y-2 w-56"
                                    onsubmit="return confirm('Catat pengembalian alat ini? Stok akan dipulihkan.')">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kondisi
                                            Kembali</label>
                                        <select name="kondisi_kembali" required
                                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak">Rusak Ringan</option>
                                            <option value="Hilang">Rusak Berat</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Denda (Rp):</label>
                                        <input type="number" name="denda" min="0" value="0"
                                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Terima Pengembalian
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">Tidak ada peminjaman yang sedang aktif
                                saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
