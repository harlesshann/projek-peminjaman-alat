@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Admin')
@section('header-title', 'Proses Pengembalian Alat')

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

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 max-w-2xl">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Form Proses Pengembalian</h3>
        </div>

        <form action="{{ route('admin.pengembalian.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Peminjaman (status dipinjam)</label>
                <select name="peminjaman_id"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('peminjaman_id') border-red-500 @enderror">
                    <option value="">-- Pilih Peminjaman --</option>
                    @foreach ($peminjamans as $p)
                        <option value="{{ $p->id }}" {{ old('peminjaman_id') == $p->id ? 'selected' : '' }}>
                            #{{ $p->id }} - {{ $p->user->name ?? 'User' }}
                            ({{ $p->tgl_pinjam }} s/d {{ $p->tgl_kembali_plan }})
                        </option>
                    @endforeach
                </select>
                @error('peminjaman_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Barang Kembali</label>
                <input type="text" name="kondisi_kembali" value="{{ old('kondisi_kembali') }}"
                    placeholder="Contoh: Lengkap dan Berfungsi Baik"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kondisi_kembali') border-red-500 @enderror">
                @error('kondisi_kembali')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Denda (Rp)</label>
                    <input type="number" name="denda" value="{{ old('denda', 0) }}" min="0"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('denda') border-red-500 @enderror">
                    @error('denda')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali</label>
                    <input type="date" name="tgl_kembali" value="{{ old('tgl_kembali') }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tgl_kembali') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Kosongkan untuk menggunakan tanggal hari ini.</p>
                    @error('tgl_kembali')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 text-sm font-semibold rounded-lg transition">
                    Proses Pengembalian
                </button>
                <a href="{{ route('admin.pengembalian.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 text-sm font-semibold rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
