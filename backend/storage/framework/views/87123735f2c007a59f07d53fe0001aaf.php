<?php $__env->startSection('title', 'Kelola Pengembalian - Panel Admin'); ?>
<?php $__env->startSection('header-title', 'Manajemen Pengembalian'); ?>

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
            <h3 class="text-lg font-bold text-gray-800">Daftar Pengembalian Alat</h3>

            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <a href="<?php echo e(route('admin.pengembalian.create')); ?>"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-semibold rounded-lg transition whitespace-nowrap">
                    + Proses Pengembalian
                </a>

                <!-- Form Search -->
                <form action="<?php echo e(route('admin.pengembalian.index')); ?>" method="GET" class="flex w-full md:w-72">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                        placeholder="Cari nama peminjam / alat..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                        Cari
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat yang Dikembalikan</th>
                        <th class="py-3 px-4 border-b">Tgl Kembali</th>
                        <th class="py-3 px-4 border-b">Kondisi</th>
                        <th class="py-3 px-4 border-b">Denda</th>
                        <th class="py-3 px-4 border-b">Petugas</th>
                        <th class="py-3 px-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $pengembalians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pengembalian): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                <?php echo e($pengembalian->peminjaman->user->name ?? 'User Dihapus'); ?>

                            </td>
                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside space-y-1">
                                    <?php $__currentLoopData = $pengembalian->peminjaman->detailPinjams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <span
                                                class="font-semibold"><?php echo e($detail->alat->nama_alat ?? 'Alat Dihapus'); ?></span>
                                            <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">(<?php echo e($detail->jumlah); ?>

                                                pcs)</span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </td>
                            <td class="py-3 px-4 border-b text-xs text-gray-600">
                                <?php echo e($pengembalian->tgl_kembali); ?>

                            </td>
                            <td class="py-3 px-4 border-b">
                                <span
                                    class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                    <?php if($pengembalian->kondisi_kembali == 'Baik'): ?> bg-emerald-100 text-emerald-800 
                                    <?php elseif($pengembalian->kondisi_kembali == 'Rusak'): ?> bg-red-100 text-red-800 
                                    <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                    <?php echo e(ucfirst($pengembalian->kondisi_kembali)); ?>

                                </span>
                            </td>
                            <td
                                class="py-3 px-4 border-b font-semibold <?php echo e($pengembalian->denda > 0 ? 'text-red-600' : 'text-gray-500'); ?>">
                                <?php if($pengembalian->denda > 0): ?>
                                    Rp <?php echo e(number_format($pengembalian->denda, 0, ',', '.')); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border-b text-gray-600">
                                <?php echo e($pengembalian->petugas->name ?? 'Petugas'); ?>

                            </td>
                            <td class="py-3 px-4 border-b text-center whitespace-nowrap">
                                <a href="<?php echo e(route('admin.pengembalian.edit', $pengembalian->id)); ?>"
                                    class="inline-block bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 text-xs font-semibold rounded-lg transition">
                                    Edit
                                </a>
                                <form action="<?php echo e(route('admin.pengembalian.destroy', $pengembalian->id)); ?>" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Batalkan pengembalian ini? Stok dan status peminjaman akan dikembalikan.')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 text-xs font-semibold rounded-lg transition">
                                        Batalkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="py-4 text-center text-gray-500">Belum ada data pengembalian.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <?php echo e($pengembalians->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/pengembalian/index.blade.php ENDPATH**/ ?>