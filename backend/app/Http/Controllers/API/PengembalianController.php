<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengembalian\StorePengembalianRequest;
use App\Http\Requests\Pengembalian\UpdatePengembalianRequest;
use App\Http\Resources\PengembalianResource;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    /**
     * Relasi standar yang selalu dimuat agar output JSON informatif.
     */
    private const RELATIONS = ['peminjaman.user', 'peminjaman.detailPinjams.alat', 'petugas'];

    /**
     * Daftar pengembalian dengan filter, pencarian, dan pagination.
     * Peminjam hanya melihat pengembalian miliknya sendiri.
     */
    public function index(Request $request): JsonResponse
    {
        $user  = auth()->user();
        $query = Pengembalian::with(self::RELATIONS);

        if ($user->role === 'peminjam') {
            $query->whereHas('peminjaman', fn ($q) => $q->where('user_id', $user->id));
        }

        // Filter langsung pada tabel pengembalian
        $query->when($request->filled('peminjaman_id'), function ($q) use ($request) {
            $q->where('peminjaman_id', $request->integer('peminjaman_id'));
        });

        $query->when($request->filled('petugas_id'), function ($q) use ($request) {
            $q->where('petugas_id', $request->integer('petugas_id'));
        });

        $query->when($request->filled('kondisi_kembali'), function ($q) use ($request) {
            $q->where('kondisi_kembali', 'like', '%' . $request->string('kondisi_kembali') . '%');
        });

        // Filter status peminjaman terkait (dipinjam/dikembalikan/telat/diajukan)
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->whereHas('peminjaman', fn ($q2) => $q2->where('status', $request->status));
        });

        // Filter rentang tanggal kembali
        $query->when($request->filled('tgl_kembali_from'), function ($q) use ($request) {
            $q->whereDate('tgl_kembali', '>=', $request->date('tgl_kembali_from'));
        });

        $query->when($request->filled('tgl_kembali_until'), function ($q) use ($request) {
            $q->whereDate('tgl_kembali', '<=', $request->date('tgl_kembali_until'));
        });

        // Pencarian bebas: kondisi kembali atau nama peminjam
        $query->when($request->filled('q'), function ($q) use ($request) {
            $q->where(function ($q2) use ($request) {
                $q2->where('kondisi_kembali', 'like', '%' . $request->string('q') . '%')
                    ->orWhereHas('peminjaman', fn ($q3) =>
                        $q3->whereHas('user', fn ($q4) => $q4->where('name', 'like', '%' . $request->string('q') . '%')));
            });
        });

        $perPage = max(1, min($request->integer('per_page', 15), 100));

        $pengembalian = $query->latest()->paginate($perPage);

        return PengembalianResource::collection($pengembalian)
            ->additional(['message' => 'Daftar pengembalian berhasil diambil.'])
            ->response();
    }

    /**
     * Detail satu pengembalian.
     */
    public function show(Pengembalian $pengembalian): JsonResponse
    {
        $user = auth()->user();
        $pengembalian->load(self::RELATIONS);

        // Otorisasi privasi: peminjam tidak boleh melihat milik orang lain
        if ($user->role === 'peminjam' && $pengembalian->peminjaman->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'message' => 'Detail pengembalian berhasil diambil.',
            'data'    => new PengembalianResource($pengembalian),
        ]);
    }

    /**
     * Proses pengembalian alat oleh petugas.
     */
    public function store(StorePengembalianRequest $request): JsonResponse
    {
        try {
            $pengembalian = DB::transaction(function () use ($request) {
                // Kunci baris peminjaman selama transaksi agar aman dari proses paralel
                $peminjaman = Peminjaman::with('detailPinjams')
                    ->lockForUpdate()
                    ->findOrFail($request->peminjaman_id);

                // Hanya peminjaman berstatus 'dipinjam' yang boleh dikembalikan
                if ($peminjaman->status !== 'dipinjam') {
                    throw new Exception("Pengembalian ditolak. Status peminjaman saat ini '{$peminjaman->status}', bukan 'dipinjam'.");
                }

                // Tentukan keterlambatan berdasarkan tanggal rencana kembali.
                // Admin boleh mencatat tanggal kembali historis via 'tgl_kembali'.
                $rencanaKembali = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
                $tglKembali     = $request->filled('tgl_kembali')
                    ? Carbon::parse($request->tgl_kembali)->startOfDay()
                    : Carbon::now()->startOfDay();
                $statusAkhir = $tglKembali->greaterThan($rencanaKembali) ? 'telat' : 'dikembalikan';

                // 1. Simpan catatan pengembalian
                $pengembalian = Pengembalian::create([
                    'peminjaman_id'   => $peminjaman->id,
                    'tgl_kembali'     => $tglKembali->toDateString(),
                    'kondisi_kembali' => $request->kondisi_kembali,
                    'denda'           => $request->denda ?? 0,
                    'petugas_id'      => auth()->id(),
                ]);

                // 2. Perbarui status peminjaman induk
                $peminjaman->update(['status' => $statusAkhir]);

                // 3. Kembalikan (tambah) stok setiap alat yang dipinjam
                foreach ($peminjaman->detailPinjams as $detail) {
                    Alat::lockForUpdate()
                        ->findOrFail($detail->alat_id)
                        ->increment('stok', $detail->jumlah);
                }

                return $pengembalian->load(self::RELATIONS);
            });

            return response()->json([
                'message' => 'Pengembalian alat berhasil diproses.',
                'data'    => new PengembalianResource($pengembalian),
            ], 201);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Koreksi data pengembalian (kondisi, denda, dan/atau tanggal kembali).
     */
    public function update(UpdatePengembalianRequest $request, Pengembalian $pengembalian): JsonResponse
    {
        $pengembalian->update($request->validated());

        return response()->json([
            'message' => 'Data pengembalian berhasil diperbarui.',
            'data'    => new PengembalianResource($pengembalian->load(self::RELATIONS)),
        ]);
    }

    /**
     * Batalkan pengembalian: kembalikan stok & status peminjaman ke kondisi semula.
     */
    public function destroy(Pengembalian $pengembalian): JsonResponse
    {
        try {
            DB::transaction(function () use ($pengembalian) {
                $peminjaman = Peminjaman::with('detailPinjams')
                    ->lockForUpdate()
                    ->findOrFail($pengembalian->peminjaman_id);

                // Tarik kembali stok yang tadi ditambahkan saat pengembalian
                foreach ($peminjaman->detailPinjams as $detail) {
                    $alat = Alat::lockForUpdate()->findOrFail($detail->alat_id);

                    if ($alat->stok < $detail->jumlah) {
                        throw new Exception("Gagal membatalkan. Stok alat '{$alat->nama_alat}' tidak cukup untuk ditarik kembali.");
                    }

                    $alat->decrement('stok', $detail->jumlah);
                }

                // Kembalikan status peminjaman menjadi 'dipinjam'
                $peminjaman->update(['status' => 'dipinjam']);

                $pengembalian->delete();
            });

            return response()->json([
                'message' => 'Pengembalian dibatalkan. Stok dan status peminjaman dikembalikan ke kondisi semula.',
            ]);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
