<?php

namespace App\Http\Requests\Pengembalian;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengembalianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kondisi_kembali' => ['nullable', 'string', 'max:255'],
            'denda'           => ['nullable', 'integer', 'min:0'],
            'tgl_kembali'     => ['nullable', 'date', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
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
            'kondisi_kembali' => 'Kondisi barang kembali',
            'denda'           => 'Nilai denda',
            'tgl_kembali'     => 'Tanggal kembali',
        ];
    }

    /**
     * Pastikan setidaknya satu field diisi saat melakukan update.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if (! $this->filled('kondisi_kembali') && ! $this->filled('denda') && ! $this->filled('tgl_kembali')) {
                $v->errors()->add('kondisi_kembali', 'Minimal satu field (kondisi_kembali, denda, atau tgl_kembali) harus diisi.');
            }
        });
    }
}
