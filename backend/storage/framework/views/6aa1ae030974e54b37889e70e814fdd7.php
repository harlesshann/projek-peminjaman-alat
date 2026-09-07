<?php $__env->startSection('title', 'Pemantauan Pengembalian - Dashboard Petugas'); ?>
<?php $__env->startSection('header-title', 'Pemantauan Pengembalian Alat'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(session('success')): ?>
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Alat Sedang Dipinjam</h3>
                <p class="text-xs text-gray-500 mt-1">Pantau alat yang belum dikembalikan lalu proses pengembaliannya.</p>
            </div>

            <form action="<?php echo e(route('petugas.pengembalian.index')); ?>" method="GET" class="flex w-full md:w-80">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama peminjam..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                    Cari
                </button>
                <?php if(request('search')): ?>
                    <a href="<?php echo e(route('petugas.pengembalian.index')); ?>"
                        class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                        Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Detail Alat</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b text-center">Status</th>
                        <th class="py-3 px-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $peminjamans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $terlambat = $item->tgl_kembali_plan && $item->tgl_kembali_plan->lt(today());
                            $selisihHari = $terlambat ? (int) $item->tgl_kembali_plan->diffInDays(today()) : 0;
                        ?>
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                <?php echo e($item->user->name ?? 'User Dihapus'); ?>

                            </td>
                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <?php $__currentLoopData = $item->detailPinjams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <span class="font-semibold"><?php echo e($detail->alat->nama_alat ?? 'Alat Dihapus'); ?></span>
                                            (Jumlah: <?php echo e($detail->jumlah); ?>)
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </td>
                            <td class="py-3 px-4 border-b whitespace-nowrap"><?php echo e($item->tgl_pinjam?->format('d M Y')); ?></td>
                            <td class="py-3 px-4 border-b whitespace-nowrap"><?php echo e($item->tgl_kembali_plan?->format('d M Y')); ?></td>
                            <td class="py-3 px-4 border-b text-center">
                                <?php if($terlambat): ?>
                                    <span class="inline-block text-xs font-semibold text-red-700 bg-red-100 px-2.5 py-1 rounded-full">
                                        Terlambat <?php echo e($selisihHari); ?> hari
                                    </span>
                                <?php else: ?>
                                    <span class="inline-block text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">
                                        Dipinjam
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <details class="group">
                                    <summary
                                        class="cursor-pointer list-none inline-block bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition shadow-sm">
                                        Proses Pengembalian
                                    </summary>
                                    <form action="<?php echo e(route('petugas.pengembalian.proses', $item->id)); ?>" method="POST"
                                        class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg space-y-2 w-56"
                                        onsubmit="return confirm('Catat pengembalian alat ini? Stok akan dipulihkan.')">
                                        <?php echo csrf_field(); ?>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kondisi Kembali</label>
                                            <select name="kondisi_kembali" required
                                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                                <option value="Baik">Baik</option>
                                                <option value="Rusak">Rusak</option>
                                                <option value="Hilang">Hilang</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Denda (Rp)</label>
                                            <input type="number" name="denda" min="0" value="0"
                                                class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                        </div>
                                        <button type="submit"
                                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                            Simpan Pengembalian
                                        </button>
                                    </form>
                                </details>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">Tidak ada alat yang sedang dipinjam.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/petugas/pengembalian/index.blade.php ENDPATH**/ ?>