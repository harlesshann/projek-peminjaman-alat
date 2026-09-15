@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Admin')
@section('header-title', 'Form Proses Pengembalian Alat')

@section('content')
    <div class="max-w-xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.pengembalian.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Transaksi Peminjaman (Aktif)</label>
                <select name="peminjaman_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Peminjam & Tanggal Rencana --</option>
                    @foreach ($peminjamans as $p)
                        <option value="{{ $p->id }}" {{ old('peminjaman_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->user->name }} (Rencana Kembali: {{ $p->tgl_kembali_plan }}) - Status:
                            {{ ucfirst($p->status) }}
                        </option>
                    @endforeach
                </select>
                @error('peminjaman_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Pengembalian Aktual</label>
                <input type="date" name="tgl_kembali" value="{{ old('tgl_kembali', date('Y-m-d')) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tgl_kembali')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Kondisi Alat Saat Kembali</label>
                <input type="text" name="kondisi_kembali" value="{{ old('kondisi_kembali', 'Baik') }}" required
                    placeholder="Contoh: Baik / Ada yang lecet / Rusak"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('kondisi_kembali')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Denda Tambahan (Opsional, misal: Alat
                    Rusak/Hilang)</label>
                <input type="number" name="denda_tambahan" value="{{ old('denda_tambahan', 0) }}" min="0"
                    placeholder="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">*Denda keterlambatan hari akan dihitung otomatis oleh sistem jika
                    melewati tanggal rencana kembali.</p>
                @error('denda_tambahan')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.pengembalian.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Proses
                    Pengembalian</button>
            </div>
        </form>
    </div>
@endsection
