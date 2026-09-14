<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetilPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PeminjamController extends Controller
{
    // Melihat daftar/katalog alat yang tersedia
    public function katalogAlat()
    {
        $alats = Alat::with('kategori')->where('stok', '>', 0)->get();
        return view('peminjam.katalog', compact('alats'));
    }

    public function ajukanPeminjaman(Request $request)
    {
        $validated = $request->validate([
            'tgl_kembali_plan' => 'required|date|after:today',
            'alat_id' => 'required|array|min:1',
            'alat_id.*' => 'exists:alat,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        $alatIds = array_values(array_unique($validated['alat_id']));
        $jumlahMap = $validated['jumlah'];

        if (count($alatIds) !== count($jumlahMap) && count($jumlahMap) !== count($validated['alat_id'])) {
            // Normalisasi: jumlah dikirim keyed by alat_id (jumlah[ID]), jadi pastikan setiap alat terpilih ada jumlahnya
            foreach ($alatIds as $id) {
                if (!isset($jumlahMap[$id])) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Jumlah pinjam tidak sesuai dengan alat yang dipilih.');
                }
            }
        }

        try {
            DB::transaction(function () use ($alatIds, $jumlahMap, $validated) {
                $peminjaman = Peminjaman::create([
                    'user_id' => Auth::id(),
                    'tgl_pinjam' => now()->toDateString(),
                    'tgl_kembali_plan' => $validated['tgl_kembali_plan'],
                    'status' => 'diajukan',
                ]);

                foreach ($alatIds as $alatId) {
                    $jumlahPinjam = (int) ($jumlahMap[$alatId] ?? 0);

                    if ($jumlahPinjam < 1) {
                        throw new \Exception('Jumlah pinjam minimal 1.');
                    }

                    // Kunci baris alat agar aman dari race condition, samakan dengan API
                    $alat = Alat::lockForUpdate()->findOrFail($alatId);

                    if ($alat->stok < $jumlahPinjam) {
                        throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi. Sisa stok: {$alat->stok}.");
                    }

                    DetilPinjam::create([
                        'peminjaman_id' => $peminjaman->id,
                        'alat_id' => $alatId,
                        'jumlah' => $jumlahPinjam,
                    ]);
                }
            });

            return redirect()->route('peminjam.riwayat')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengajukan peminjaman. ' . $e->getMessage());
        }
    }

    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with(['detailPinjams.alat', 'pengembalian'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('peminjam.riwayat', compact('peminjamans'));
    }
}
