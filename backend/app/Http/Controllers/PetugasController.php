<?php
namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PetugasController extends Controller
{
// Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();
        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    public function setujuiPeminjaman ($id)
    {
        DB:: beginTransaction();
        try {
            $peminjaman = Peminjaman:: with('detailPinjams')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    // Menampilkan daftar alat yang sedang dipinjam untuk dipantau pengembaliannya
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian'])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.pengembalian.index', compact('peminjamans', 'search'));
    }

    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($peminjamanId);

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);
            
            $peminjaman->update(['status' => 'dikembalikan']);

            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian Berhasil dicatat dan stok dipulihkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi Kesalahan: '. $e->getMessage());
        }
    }

    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Pastikan statusnya memang masih diajukan
            if ($peminjaman->status == 'diajukan') {
                $peminjaman->delete();
                return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
            }

            return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman filter + preview laporan (menu "Cetak Laporan").
     * GET /petugas/laporan?start_date=&end_date=&status=
     */
    public function indexLaporan(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'status' => 'nullable|in:diajukan,dipinjam,dikembalikan,telat',
        ]);

        $baseQuery = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian.petugas'])
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($q) use ($request) {
                $q->whereBetween('tgl_pinjam', [$request->start_date, $request->end_date]);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            });

        // Ringkasan dihitung dari seluruh data terfilter (tanpa pagination)
        $all = (clone $baseQuery)->latest()->get();
        $summary = [
            'total_transaksi' => $all->count(),
            'total_item' => $all->sum(fn ($p) => $p->detailPinjams->sum('jumlah')),
            'total_denda' => (int) $all->sum(fn ($p) => (int) ($p->pengembalian->denda ?? 0)),
        ];

        $peminjamans = $baseQuery->latest()->paginate(15)->withQueryString();

        return view('petugas.laporan.index', compact('peminjamans', 'summary'));
    }

    /**
     * Cetak laporan.
     * GET /petugas/laporan/cetak?start_date=&end_date=&status=&output=html|pdf&download=0|1&auto_print=0|1
     * - output=html : tampilan print browser (ada tombol Print + Download PDF)
     * - output=pdf  : stream/download PDF langsung
     */
    public function cetakLaporan(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'status' => 'nullable|in:diajukan,dipinjam,dikembalikan,telat',
            'output' => 'nullable|in:html,pdf',
            'download' => 'nullable|in:0,1',
            'auto_print' => 'nullable|in:0,1',
        ]);

        $output = $request->input('output', 'html');

        $items = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian.petugas'])
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($q) use ($request) {
                $q->whereBetween('tgl_pinjam', [$request->start_date, $request->end_date]);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest()
            ->get();

        $summary = [
            'total_transaksi' => $items->count(),
            'total_item' => $items->sum(fn ($p) => $p->detailPinjams->sum('jumlah')),
            'total_denda' => (int) $items->sum(fn ($p) => (int) ($p->pengembalian->denda ?? 0)),
            'per_status' => [
                'diajukan' => $items->where('status', 'diajukan')->count(),
                'dipinjam' => $items->where('status', 'dipinjam')->count(),
                'dikembalikan' => $items->where('status', 'dikembalikan')->count(),
                'telat' => $items->where('status', 'telat')->count(),
            ],
        ];

        $data = [
            'items' => $items,
            'summary' => $summary,
            'filter' => $request->only(['start_date', 'end_date', 'status']),
            'tanggal_cetak' => now()->translatedFormat('d F Y H:i'),
            'dicetak_oleh' => auth()->user()?->name ?? 'Sistem',
            'auto_print' => $request->input('auto_print', '0'),
            'query_string' => http_build_query($request->only(['start_date', 'end_date', 'status'])),
        ];

        if ($output === 'pdf') {
            $filename = 'laporan-peminjaman-' . now()->format('Ymd-His') . '.pdf';
            $pdf = Pdf::loadView('laporan.cetak-pdf', $data)
                ->setPaper('a4', 'landscape')
                ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);

            return $request->input('download') === '1'
                ? $pdf->download($filename)
                : $pdf->stream($filename);
        }

        return view('petugas.laporan.cetak', $data);
    }
}    