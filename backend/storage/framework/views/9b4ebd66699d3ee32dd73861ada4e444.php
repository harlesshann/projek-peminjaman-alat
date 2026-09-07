<?php $__env->startSection('title', 'Kelola User - Panel Admin'); ?>
<?php $__env->startSection('header-title', 'Manajemen Pengguna Sistem'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Notifikasi Sukses/Gagal -->
    <?php if(session('success')): ?>
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Daftar Pengguna Sistem</h3>
            <div class="flex items-center gap-3 w-full md:w-auto">
                
                <form action="<?php echo e(route('admin.user.index')); ?>" method="GET" class="flex w-full md:w-80">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                        placeholder="Cari nama, email, role..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus: ring-2 focus:ring-blue-500">
                    <button type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                        Cari
                    </button>
                    <?php if(request('search')): ?>
                        <a href="<?php echo e(route('admin.user.index')); ?>"
                            class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition"
                            title="Reset Pencarian">
                            Reset
                        </a>
                    <?php endif; ?>
                </form>

                
                <a href="<?php echo e(route('admin.user.create')); ?>"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-gl transition">
                    + Tambah User
                </a>
            </div>
            <!-- Tombol Tambah User (jika ingin dibuatkan form tambah) -->

        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b text-center">Foto</th>
                        <th class="py-3 px-4 border-b">Nama</th>
                        <th class="py-3 px-4 border-b">Email</th>
                        <th class="py-3 px-4 border-b">Role / Hak Akses</th>
                        <th class="py-3 px-4 border-b">No. HP</th>
                        <th class="py-3 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 border-b text-center">
                                <?php if($user->foto_profile): ?>
                                    <img src="<?php echo e(asset($user->foto_profile)); ?>" alt="Foto Profile"
                                        class="w-10 h-10 object-cover rounded-full mx-auto">
                                <?php else: ?>
                                    <span
                                        class="inline-flex w-10 h-10 rounded-full bg-gray-300 items-center justify-center text-white font-bold text-sm mx-auto">
                                        <?php echo e(substr($user->name, 0, 1)); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border-b font-medium text-gray-900"><?php echo e($user->name); ?></td>
                            <td class="py-3 px-4 border-b"><?php echo e($user->email); ?></td>
                            <td class="py-3 px-4 border-b">
                                <span
                                    class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                    <?php if($user->role == 'admin'): ?> bg-purple-100 text-purple-800 
                                    <?php elseif($user->role == 'petugas'): ?> bg-blue-100 text-blue-800 
                                    <?php else: ?> bg-green-100 text-green-800 <?php endif; ?>">
                                    <?php echo e(ucfirst($user->role)); ?>

                                </span>
                            </td>
                            <td class="py-3 px-4 border-b"><?php echo e($user->no_hp ?? '-'); ?></td>
                            <td class="py-3 px-4 border-b">
                                <div class="flex items-center space-x-2">
                                    <!-- Tombol Edit -->
                                    <a href="<?php echo e(route('admin.user.edit', $user->id)); ?>"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Edit
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <form action="<?php echo e(route('admin.user.destroy', $user->id)); ?>" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">Belum ada data pengguna.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <?php echo e($users->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/user/index.blade.php ENDPATH**/ ?>