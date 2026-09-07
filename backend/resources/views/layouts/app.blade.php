<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>

    <!-- Memuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">
        @if (auth()->user()->role == 'admin')
            <!-- SIDEBAR -->
            <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">
                <div class="p-5 font-bold tracking-wider border-b border-gray-800">
                    PANEL ADMIN
                </div>

                <nav class="flex-1 p-4 space-y-2">
                    <a href="{{ route('admin.dashboard') }}"
                        class="block px-4 py-2 rounded-lg bg-gray-800 text-white font-medium">
                        Dashboard
                    </a>

                    <a href="{{ route('admin.alat.index') }}"
                        class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">
                        Kelola Alat
                    </a>

                    <a href="{{ route('admin.user.index') }}"
                        class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">
                        Kelola User
                    </a>
                    <a href="{{ route('admin.kategori.index') }}"
                        class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">
                        Kelola Kategori
                    </a>
                    <!-- Menu Kelola Peminjam -->
                    <a href="{{ route('admin.peminjaman.index') }}"
                        class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.peminjaman*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white transition' }}">
                        Kelola Peminjaman</a>

                    <a href="{{ route('admin.pengembalian.index') }}"
                        class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.pengembalian*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white transition' }}">
                        Kelola Pengembalian</a>
                </nav>
            </aside>
        @endif
        <!-- MENU KHUSUS PETUGAS -->
        @if (auth()->user()->role === 'petugas')
            <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">
                <div class="p-5 font-bold tracking-wider border-b border-gray-800">
                    PANEL PETUGAS
                </div>

                <nav class="flex-1 p-4 space-y-2">
                    <a href="{{ route('petugas.peminjaman.index') }}"
                        class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.peminjaman*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Persetujuan Peminjaman</a>

                    <a href="{{ route('petugas.pengembalian.index') }}"
                        class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.pengembalian*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Pemantauan Pengembalian</a>

                    @if (Route::has('petugas.laporan.index'))
                        <a href="{{ route('petugas.laporan.index') }}"
                            class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.laporan*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            Cetak Laporan</a>
                    @endif
                </nav>

                <div class="p-4 border-t border-gray-800 text-sm text-gray-400">
                    Logged in as:
                    <span class="text-white font-semibold">
                        {{ auth()->user()->name }}
                    </span>
                </div>
            </aside>
        @endif


        <!-- MAIN CONTENT CONTAINER -->
        <div class="flex-1 flex flex-col overflow-y-auto">

            <!-- NAVBAR ATAS -->
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10">
                <div class="text-lg font-semibold text-gray-800">
                    @yield('header-title', 'Dashboard')
                </div>

                <div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- KONTEN UTAMA HALAMAN -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>

        </div>
    </div>

</body>

</html>
