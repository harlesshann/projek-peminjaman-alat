@extends('layouts.app')

@section('title', 'Edit Pengembalian - Panel Admin')
@section('header-title', 'Edit Data Pengembalian')

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
            <h3 class="text-lg font-bold text-gray-800">
                Edit Pengembalian #{{ $pengembalian->id }}
                <span class="text-sm font-normal text-gray-500">
                    — Peminjam: {{ $pengembalian->peminjaman->user->name ?? '-' }}
                </span>
            </h3>
        </div>

        <form action="{{ route('admin.pengembalian.update', $pengembalian->id) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Barang Kembali</label>
                <input type="text" name="kondisi_kembali" value="{{ old('kondisi_kembali', $pengembalian->kondisi_kembali) }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kondisi_kembali') border-red-500 @enderror">
                @error('kondisi_kembali')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Denda (Rp)</label>
                    <input type="number" name="denda" value="{{ old('denda', $pengembalian->denda) }}" min="0"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('denda') border-red-500 @enderror">
                    @error('denda')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali</label>
                    <input type="date" name="tgl_kembali" value="{{ old('tgl_kembali', $pengembalian->tgl_kembali) }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tgl_kembali') border-red-500 @enderror">
                    @error('tgl_kembali')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 text-sm font-semibold rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.pengembalian.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 text-sm font-semibold rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
