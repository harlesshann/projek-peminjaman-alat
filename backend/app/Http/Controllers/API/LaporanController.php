<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeminjamanResource;
use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LaporanController extends Controller
{
    /**
     * Query dasar laporan dengan eager loading + filter yang sama
     * dipakai oleh index (JSON) dan cetak (PDF/HTML).
     */
    private function buildFilteredQuery(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian.petugas']);

        $query->when($request->filled('start_date') && $request->filled('end_date'), function ($q) use ($request) {
            $q->whereBetween('tgl_pinjam', [$request->start_date, $request->end_date]);
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        return $query;
    }

    private function buildSummary($items): array
    {
        return [
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
    }
    public function index(Request $request): JsonResponse
    {
        // 1. Validasi Input Parameter
        $validator = Validator::make($request->all(), [
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'status' => ['nullable', 'string', 'in:diajukan,dipinjam,dikembalikan,telat'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Parameter filter tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Eager loading untuk mencegah masalah N+1 Query
        $query = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian.petugas']);

        // Filter: Rentang Tanggal Pinjam
        $query->when($request->filled('start_date') && $request->filled('end_date'), function ($q) use ($request) {
            $q->whereBetween('tgl_pinjam', [$request->start_date, $request->end_date]);
        });

        // Filter: Status Peminjaman
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        // (Pagination)
        $perPage = $request->input('per_page', 15);
        $laporan = $query->latest()->paginate($perPage);

        // API Resource khusus untuk instance Paginate
        return PeminjamanResource::collection($laporan)
            ->additional([
                'message' => 'Laporan peminjaman berhasil ditarik.'
            ])
            ->response();
    }

    /**
     * Cetak laporan peminjaman.
     *
     * GET /api/laporan-peminjaman/cetak?start_date=2024-01-01&end_date=2024-12-31&status=dipinjam&format=pdf&download=0
     *
     * Query params:
     * - start_date, end_date (Y-m-d, opsional tapi harus sepasang)
     * - status: diajukan|dipinjam|dikembalikan|telat (opsional)
     * - format: pdf|html (default pdf). html = preview print browser.
     * - download: 1 = attachment download, 0 = stream inline (default 0)
     */
    public function cetak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'status' => ['nullable', 'string', 'in:diajukan,dipinjam,dikembalikan,telat'],
            'format' => ['nullable', 'string', 'in:pdf,html'],
            'download' => ['nullable', 'in:0,1,true,false'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Parameter cetak tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $format = $request->input('format', 'pdf');
        $items = $this->buildFilteredQuery($request)->latest()->get();
        $summary = $this->buildSummary($items);

        $data = [
            'items' => $items,
            'summary' => $summary,
            'filter' => $request->only(['start_date', 'end_date', 'status']),
            'tanggal_cetak' => now()->translatedFormat('d F Y H:i'),
            'dicetak_oleh' => $request->user()?->name ?? 'Sistem',
        ];

        // Preview HTML untuk print browser (window.print)
        if ($format === 'html') {
            return view('laporan.cetak-pdf', $data);
        }

        $filename = 'laporan-peminjaman-' . now()->format('Ymd-His') . '.pdf';

        $pdf = Pdf::loadView('laporan.cetak-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);

        $shouldDownload = in_array($request->input('download'), [1, '1', true, 'true'], true);

        return $shouldDownload
            ? $pdf->download($filename)
            : $pdf->stream($filename);
    }
}