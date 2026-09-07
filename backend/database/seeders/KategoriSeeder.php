<?php

namespace Database\Seeders;
use App\Models\Kategori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            ['nama_kategori' => 'Jaringan & Konektivitas'],
            ['nama_kategori' => 'Multimedia & Audio Visual'],
            ['nama_kategori' => 'Pemrograman Pemrosesan'],
            ['nama_kategori' => 'Perkakas & Elektronik'],
            ['nama_kategori' => 'Suku Cadang & Aksesoris'],
        ];

        foreach ($kategori as $kat) {
            Kategori::create($kat);
        }
    }
}
