<?php $__env->startSection('title', 'Persetujuan Peminjaman - Dashboard Petugas'); ?>
<?php $__env->startSection('header-title', 'Daftar Pengajuan Peminjaman Alat'); ?>

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
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Menunggu Verifikasi Persetujuan</h3>
            <form action="<?php echo e(route('petugas.peminjaman.index')); ?>" method="GET" class="flex w-full md:w-80">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama peminjam..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                    Cari
                </button>
                <?php if(request('search')): ?>
                    <a href="<?php echo e(route('petugas.peminjaman.index')); ?>"
                        class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                        Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b">Peminjam</th>
                    <th class="py-3 px-4 border-b">Tanggal Pinjam</th>
                    <th class="py-3 px-4 border-b">Rencana Kembali</th>
                    <th class="py-3 px-4 border-b">Detail Alat</th>
                    <th class="py-3 px-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                <?php $__empty_1 = true; $__currentLoopData = $peminjamans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition align-top">
                        <td class="py-3 px-4 border-b font-medium text-gray-900">
                            <?php echo e($item->user->name ?? 'User Dihapus'); ?>

                        </td>
                        <td class="py-3 px-4 border-b"><?php echo e($item->tgl_pinjam); ?></td>
                        <td class="py-3 px-4 border-b"><?php echo e($item->tgl_kembali_plan); ?></td>
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
                        <td class="py-3 px-4 border-b">
                            <?php if($item->status == 'diajukan'): ?>
                                <div class="flex items-center space-x-2">
                                    <form action="<?php echo e(route('petugas.peminjaman.setujui', $item->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" onclick="return confirm('Setujui peminjaman alat ini?')"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition shadow-sm">
                                            Setujui
                                        </button>
                                    </form>
                                    <!-- Tombol Tolak -->
                                    <form action="<?php echo e(route('petugas.peminjaman.tolak', $item->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                            onclick="return confirm('Yakin ingin menolak pengajuan peminjaman ini?')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition shadow-sm">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded">
                                    <?php echo e(ucfirst($item->status)); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada pengajuan peminjaman baru.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/petugas/peminjaman/index.blade.php ENDPATH**/ ?>