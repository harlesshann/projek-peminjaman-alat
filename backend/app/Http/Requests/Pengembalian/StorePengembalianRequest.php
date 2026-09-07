<?php

namespace App\Http\Requests\Pengembalian;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePengembalianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'peminjaman_id' => [
                'required',
                'integer',
                Rule::exists('peminjaman', 'id'),
                // Cegah satu peminjaman diproses pengembaliannya lebih dari sekali
                Rule::unique('pengembalian', 'peminjaman_id'),
            ],
            'kondisi_kembali' => ['required', 'string', 'max:255'],
            'denda'           => ['nullable', 'integer', 'min:0'],
            'tgl_kembali'     => ['nullable', 'date', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'peminjaman_id.required'   => 'ID peminjaman wajib diisi.',
            'peminjaman_id.exists'     => 'Data peminjaman tidak ditemukan.',
            'peminjaman_id.unique'     => 'Peminjaman ini sudah pernah dikembalikan.',
            'kondisi_kembali.required' => 'Kondisi barang saat dikembalikan wajib diisi.',
            'denda.integer'            => 'Nilai denda harus berupa angka.',
            'denda.min'                => 'Nilai denda tidak boleh negatif.',
            'tgl_kembali.date'         => 'Format tanggal kembali tidak valid.',
            'tgl_kembali.date_format'  => 'Format tanggal kembali harus YYYY-MM-DD.',
        ];
    }

    public function attributes(): array
    {
        return [
            'peminjaman_id'   => 'ID Peminjaman',
            'kondisi_kembali' => 'Kondisi barang kembali',
            'denda'           => 'Nilai denda',
        ];
    }
}
