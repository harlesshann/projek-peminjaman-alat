<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Alat</title>
    <style>
        * { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
        body { font-size: 11px; color: #111; margin: 20px 24px; }
        .kop { text-align: center; border-bottom: 3px double #111; padding-bottom: 10px; margin-bottom: 14px; }
        .kop h1 { font-size: 18px; margin: 0; text-transform: uppercase; }
        .kop h2 { font-size: 14px; margin: 2px 0; font-weight: normal; }
        .kop p { font-size: 10px; margin: 2px 0; color: #444; }
        .judul { text-align: center; margin: 10px 0; }
        .judul h3 { font-size: 15px; margin: 0; text-decoration: underline; }
        .judul p { font-size: 10px; margin: 4px 0; color: #333; }
        .meta { width: 100%; margin: 10px 0; font-size: 10px; }
        .meta td { padding: 2px 4px; vertical-align: top; }
        .ringkasan { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 10px; }
        .ringkasan th, .ringkasan td { border: 1px solid #555; padding: 5px 8px; text-align: center; }
        .ringkasan th { background: #eee; }
        table.data { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.data th, table.data td { border: 1px solid #555; padding: 5px 6px; vertical-align: top; }
        table.data th { background: #e8e8e8; text-align: center; }
        table.data td.center { text-align: center; }
        table.data td.right { text-align: right; }
        ul.alat { margin: 0; padding-left: 14px; }
        .ttd { width: 100%; margin-top: 26px; font-size: 11px; }
        .ttd td { text-align: center; vertical-align: top; }
        .footer { margin-top: 14px; font-size: 9px; color: #555; text-align: right; font-style: italic; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>Sistem Peminjaman Alat</h1>
        <h2>Laporan Data Peminjaman</h2>
        <p>Jl. Pendidikan No. 1 &bull; Telp: (021) 000-0000 &bull; Email: info@peminjaman.test</p>
    </div>

    <div class="judul">
        <h3>LAPORAN PEMINJAMAN ALAT</h3>
        <p>
            @if (!empty($filter['start_date']) && !empty($filter['end_date']))
                Periode: {{ \Carbon\Carbon::parse($filter['start_date'])->translatedFormat('d F Y') }}
                s/d {{ \Carbon\Carbon::parse($filter['end_date'])->translatedFormat('d F Y') }}
            @else
                Periode: Semua Data
            @endif
            &bull;
            Status:
            {{ !empty($filter['status']) ? ucfirst($filter['status']) : 'Semua' }}
        </p>
    </div>

    <table class="meta">
        <tr>
            <td width="18%">Tanggal Cetak</td>
            <td width="2%">:</td>
            <td width="40%">{{ $tanggal_cetak ?? now()->translatedFormat('d F Y H:i') }}</td>
            <td width="18%">Dicetak Oleh</td>
            <td width="2%">:</td>
            <td>{{ $dicetak_oleh ?? 'Sistem' }}</td>
        </tr>
        <tr>
            <td>Total Transaksi</td>
            <td>:</td>
            <td>{{ $summary['total_transaksi'] ?? $items->count() }} transaksi</td>
            <td>Total Denda</td>
            <td>:</td>
            <td>Rp {{ number_format($summary['total_denda'] ?? 0, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if (isset($summary['per_status']))
        <table class="ringkasan">
            <tr>
                <th>Diajukan</th>
                <th>Dipinjam</th>
                <th>Dikembalikan</th>
                <th>Telat</th>
                <th>Total Item Dipinjam</th>
            </tr>
            <tr>
                <td>{{ $summary['per_status']['diajukan'] }}</td>
                <td>{{ $summary['per_status']['dipinjam'] }}</td>
                <td>{{ $summary['per_status']['dikembalikan'] }}</td>
                <td>{{ $summary['per_status']['telat'] }}</td>
                <td>{{ $summary['total_item'] ?? '-' }} pcs</td>
            </tr>
        </table>
    @endif

    <table class="data">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="14%">Peminjam</th>
                <th width="24%">Alat Dipinjam</th>
                <th width="10%">Tgl Pinjam</th>
                <th width="10%">Rencana Kembali</th>
                <th width="10%">Tgl Kembali</th>
                <th width="9%">Status</th>
                <th width="10%">Denda</th>
                <th width="9%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $i => $p)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $p->user->name ?? 'User Dihapus' }}</td>
                    <td>
                        <ul class="alat">
                            @foreach ($p->detailPinjams as $d)
                                <li>{{ $d->alat->nama_alat ?? 'Alat Dihapus' }} ({{ $d->jumlah }} pcs)</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="center">{{ $p->tgl_pinjam?->format('d/m/Y') }}</td>
                    <td class="center">{{ $p->tgl_kembali_plan?->format('d/m/Y') }}</td>
                    <td class="center">{{ $p->pengembalian?->tgl_kembali?->format('d/m/Y') ?? '-' }}</td>
                    <td class="center">{{ ucfirst($p->status) }}</td>
                    <td class="right">Rp {{ number_format((int) ($p->pengembalian->denda ?? 0), 0, ',', '.') }}</td>
                    <td class="center">{{ $p->pengembalian?->petugas?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center">Tidak ada data pada periode / filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="ttd">
        <tr>
            <td width="60%"></td>
            <td width="40%">
                {{ now()->translatedFormat('d F Y') }}<br>
                Petugas,<br><br><br><br>
                <strong><u>{{ $dicetak_oleh ?? '....................' }}</u></strong>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dicetak otomatis dari Sistem Peminjaman Alat pada {{ $tanggal_cetak ?? now() }}.
    </div>
</body>
</html>
