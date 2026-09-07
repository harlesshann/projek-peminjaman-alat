<?php $__env->startSection('title', 'Kelola Peminjaman - Panel Admin'); ?>
<?php $__env->startSection('header-title', 'Manajemen Transaksi Peminjaman'); ?>

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
            <h3 class="text-lg font-bold text-gray-800">Daftar Transaksi Peminjaman</h3>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Form Search -->
                <form action="<?php echo e(route('admin.peminjaman.index')); ?>" method="GET" class="flex w-full md:w-80">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                        placeholder="Cari nama peminjam / status..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                        Cari
                    </button>
                    <?php if(request('search')): ?>
                        <a href="<?php echo e(route('admin.peminjaman.index')); ?>"
                            class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                            Reset
                        </a>
                    <?php endif; ?>
                </form>

                <!-- Tombol Tambah -->
                <a href="<?php echo e(route('admin.peminjaman.create')); ?>"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                    + Tambah Peminjaman
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat yang Dipinjam</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam / Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $peminjamans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $peminjaman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                <?php echo e($peminjaman->user->name ?? 'User Dihapus'); ?>

                            </td>
                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside space-y-1">
                                    <?php $__currentLoopData = $peminjaman->detailPinjams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                <span class="block"><span class="font-semibold">Pinjam:</span>
                                    <?php echo e($peminjaman->tgl_pinjam); ?></span>
                                <span class="block"><span class="font-semibold">Rencana:</span>
                                    <?php echo e($peminjaman->tgl_kembali_plan); ?></span>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <span
                                    class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                    <?php if($peminjaman->status == 'diajukan'): ?> bg-yellow-100 text-yellow-800 
                                    <?php elseif($peminjaman->status == 'dipinjam'): ?> bg-blue-100 text-blue-800 
                                    <?php elseif($peminjaman->status == 'dikembalikan'): ?> bg-emerald-100 text-emerald-800 
                                    <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                                    <?php echo e(ucfirst($peminjaman->status)); ?>

                                </span>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <div class="flex flex-col space-y-2">
                                    <!-- Form Ubah Status Cepat -->
                                    <form action="<?php echo e(route('admin.peminjaman.updateStatus', $peminjaman->id)); ?>"
                                        method="POST" class="flex items-center space-x-1">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <select name="status" onchange="this.form.submit()"
                                            class="text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none">
                                            <option value="diajukan"
                                                <?php echo e($peminjaman->status == 'diajukan' ? 'selected' : ''); ?>>Diajukan</option>
                                            <option value="dipinjam"
                                                <?php echo e($peminjaman->status == 'dipinjam' ? 'selected' : ''); ?>>Dipinjam</option>
                                            <option value="dikembalikan"
                                                <?php echo e($peminjaman->status == 'dikembalikan' ? 'selected' : ''); ?>>Selesai
                                            </option>
                                            <option value="telat" <?php echo e($peminjaman->status == 'telat' ? 'selected' : ''); ?>>
                                                Telat</option>
                                        </select>
                                    </form>

                                    <!-- Tombol Hapus -->
                                    <form action="<?php echo e(route('admin.peminjaman.destroy', $peminjaman->id)); ?>" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data peminjaman ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition w-full">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500">Belum ada data peminjaman.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <?php echo e($peminjamans->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/peminjaman/index.blade.php ENDPATH**/ ?>