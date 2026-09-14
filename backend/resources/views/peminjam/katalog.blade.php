@extends('layouts.app')

@section('title', 'Katalog Alat - Panel Peminjam')
@section('header-title', 'Katalog Alat Tersedia')

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

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Ajukan Peminjaman Alat</h3>
            <p class="text-sm text-gray-500 mt-1">Centang alat yang ingin dipinjam, atur jumlah, lalu tentukan rencana tanggal kembali.</p>
        </div>

        <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
            @csrf
            <div class="p-5 border-b border-gray-200 bg-white">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rencana Tanggal Kembali</label>
                <input type="date" name="tgl_kembali_plan" value="{{ old('tgl_kembali_plan') }}" required
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    class="w-full md:w-80 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tgl_kembali_plan') border-red-500 @enderror">
                @error('tgl_kembali_plan')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                            <th class="py-3 px-4 border-b text-center w-16">Pilih</th>
                            <th class="py-3 px-4 border-b">Nama Alat</th>
                            <th class="py-3 px-4 border-b">Kategori</th>
                            <th class="py-3 px-4 border-b">Stok Tersedia</th>
                            <th class="py-3 px-4 border-b w-40">Jumlah Pinjam</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @forelse($alats as $alat)
                            <tr class="hover:bg-gray-50 transition align-middle">
                                <td class="py-3 px-4 border-b text-center">
                                    <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}"
                                        {{ in_array($alat->id, old('alat_id', [])) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $alat->nama_alat }}</td>
                                <td class="py-3 px-4 border-b">{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                                <td class="py-3 px-4 border-b font-semibold">{{ $alat->stok }}</td>
                                <td class="py-3 px-4 border-b">
                                    <input type="number" name="jumlah[{{ $alat->id }}]"
                                        value="{{ old('jumlah.' . $alat->id, 1) }}" min="1"
                                        max="{{ $alat->stok }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada alat yang tersedia saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row gap-3 sm:items-center">
                <button type="submit" @if ($alats->isEmpty()) disabled @endif
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-sm">
                    Ajukan Peminjaman
                </button>
                <a href="{{ route('peminjam.riwayat') }}"
                    class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-4 py-2 rounded-lg transition text-center">
                    Lihat Riwayat Pinjam
                </a>
            </div>
        </form>
    </div>
@endsection
