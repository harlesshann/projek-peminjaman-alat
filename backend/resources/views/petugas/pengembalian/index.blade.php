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
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Alat Sedang Dipinjam</h3>
                <p class="text-xs text-gray-500 mt-1">Pantau alat yang belum dikembalikan lalu proses pengembaliannya.</p>
            </div>

            <form action="{{ route('petugas.pengembalian.index') }}" method="GET" class="flex w-full md:w-80">
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
                        <th class="py-3 px-4 border-b">Detail Alat</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b text-center">Status</th>
                        <th class="py-3 px-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjamans as $item)
                        @php
                            $terlambat = $item->tgl_kembali_plan && $item->tgl_kembali_plan->lt(today());
                            $selisihHari = $terlambat ? (int) $item->tgl_kembali_plan->diffInDays(today()) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                {{ $item->user->name ?? 'User Dihapus' }}
                            </td>
                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    @foreach ($item->detailPinjams as $detail)
                                        <li>
                                            <span class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            (Jumlah: {{ $detail->jumlah }})
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3 px-4 border-b whitespace-nowrap">{{ $item->tgl_pinjam?->format('d M Y') }}</td>
                            <td class="py-3 px-4 border-b whitespace-nowrap">{{ $item->tgl_kembali_plan?->format('d M Y') }}</td>
                            <td class="py-3 px-4 border-b text-center">
                                @if ($terlambat)
                                    <span class="inline-block text-xs font-semibold text-red-700 bg-red-100 px-2.5 py-1 rounded-full">
                                        Terlambat {{ $selisihHari }} hari
                                    </span>
                                @else
                                    <span class="inline-block text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">
                                        Dipinjam
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 border-b">
                                <details class="group">
                                    <summary
                                        class="cursor-pointer list-none inline-block bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition shadow-sm">
                                        Proses Pengembalian
                                    </summary>
                                    <form action="{{ route('petugas.pengembalian.proses', $item->id) }}" method="POST"
                                        class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg space-y-2 w-56"
                                        onsubmit="return confirm('Catat pengembalian alat ini? Stok akan dipulihkan.')">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kondisi Kembali</label>
                                            <select name="kondisi_kembali" required
                                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                                <option value="Baik">Baik</option>
                                                <option value="Rusak">Rusak</option>
                                                <option value="Hilang">Hilang</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Denda (Rp)</label>
                                            <input type="number" name="denda" min="0" value="0"
                                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                        </div>
                                        <button type="submit"
                                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                            Simpan Pengembalian
                                        </button>
                                    </form>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">Tidak ada alat yang sedang dipinjam.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
