# Projek Peminjaman Alat — API Ujikom

REST API peminjaman alat (Laravel 12 + Sanctum + MySQL 8 + Docker) — roles `admin / petugas / peminjam`, katalog alat, peminjaman, pengembalian, laporan & log aktivitas.

> Repo: `api-peminjaman-alat` (public). Folder `backend/` berisi aplikasi Laravel, root berisi `docker-compose.yml` untuk MySQL + Laravel + phpMyAdmin.

## Fitur
- Auth: `POST /api/register`, `POST /api/login`, `GET /api/me`, `POST /api/logout` (Sanctum)
- Katalog: `GET /api/katalog`, `GET /api/alat`
- Admin: CRUD `kategori`, `alat`, `users`, `peminjaman`, `pengembalian`, `GET /api/log-aktivitas`, `GET /api/laporan-peminjaman`
- Petugas: `POST /api/peminjaman/{id}/approve`, CRUD `pengembalian`
- Peminjam: `POST /api/peminjaman`, `GET /api/riwayat-pinjam`
- Pemantauan pengembalian: filter `peminjaman_id, petugas_id, kondisi_kembali, status, tgl_kembali_from/until, q, per_page`, hitung `telat / dikembalikan`, update stok atomik via transaction + `lockForUpdate`

## Prasyarat
- Docker + Docker Compose
- PHP 8.2 + Composer (untuk jalan tanpa Docker / install dep)

## Quickstart (Docker - disarankan)
```bash
cp backend/.env.example backend/.env
# sesuaikan DB_HOST=mysql-server, DB_DATABASE=API_ujikom, DB_USERNAME=api_ujikom, DB_PASSWORD=api_ujikom
docker compose up --build -d
docker exec -it laravel-api composer install
docker exec -it laravel-api php artisan key:generate
docker exec -it laravel-api php artisan migrate --seed

1. Persiapan WSL (di Windows, PowerShell admin)
wsl --install -d Ubuntu
Restart kalau diminta, buat user + password Ubuntu.
2. Install tool di WSL Ubuntu
sudo apt update && sudo apt install -y git curl
3. Install Docker — pilih salah satu:
- Docker Desktop di Windows (recommended): aktifkan Settings → Resources → WSL integration → Ubuntu. Command docker langsung jalan di WSL.
- Docker Engine langsung di WSL: ikuti https://docs.docker.com/engine/install/ubuntu/
Cek: docker --version && docker compose version
4. Clone repo
git clone https://github.com/harlesshann/projek-peminjaman-alat.git
cd projek-peminjaman-alat
5. Setup env
cp backend/.env.example backend/.env
Pastikan isi .env sesuai docker-compose:
DB_HOST=mysql-server
DB_DATABASE=API_ujikom
DB_USERNAME=api_ujikom
DB_PASSWORD=api_ujikom
6. Jalankan
docker compose up --build -d
docker exec -it laravel-api composer install
docker exec -it laravel-api php artisan key:generate
docker exec -it laravel-api php artisan migrate --seed
7. Akses
- API: http://localhost:8000/api
- phpMyAdmin: http://localhost:8081 (host mysql-server, db API_ujikom)
```

Akses:
- API: `http://localhost:8000/api`
- phpMyAdmin: `http://localhost:8081` (host `mysql-server`)
- MySQL: `localhost:3306`, db `API_ujikom`

## Struktur
```
.
├── docker-compose.yml   # mysql-server, app-laravel, phpmyadmin
├── backend/             # Laravel 12 (routes/api.php, app/Models, app/Http/Controllers/API)
└── README.md
```

## Keamanan untuk repo public
- `backend/.env`, `backend/vendor/`, `*.zip` TIDAK di-commit (lihat `.gitignore`)
- Password di `docker-compose.yml` hanya untuk dev lokal, ganti di production
- Salin dari `.env.example` untuk setup baru

## Lisensi
MIT
