<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengembalianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'tgl_kembali'     => $this->tgl_kembali?->format('Y-m-d'),
            'kondisi_kembali' => $this->kondisi_kembali,
            'denda'           => (int) $this->denda,
            'petugas'         => $this->whenLoaded('petugas', fn () => $this->petugas?->name),
            'peminjaman'      => $this->whenLoaded('peminjaman', function () {
                return [
                    'id'               => $this->peminjaman->id,
                    'peminjam'         => $this->peminjaman->user?->name,
                    'tgl_pinjam'       => $this->peminjaman->tgl_pinjam?->format('Y-m-d'),
                    'tgl_kembali_plan' => $this->peminjaman->tgl_kembali_plan?->format('Y-m-d'),
                    'status'           => $this->peminjaman->status,
                    'items'            => $this->peminjaman->relationLoaded('detailPinjams')
                        ? $this->peminjaman->detailPinjams->map(fn ($detail) => [
                            'nama_alat' => $detail->alat?->nama_alat ?? 'Alat tidak ditemukan',
                            'jumlah'    => (int) $detail->jumlah,
                        ])
                        : null,
                ];
            }),
            'created_at'      => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
