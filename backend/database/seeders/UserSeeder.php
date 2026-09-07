<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Bagus Karim',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'no_hp' => '081234567890',
                'alamat' => 'Bandung, West Java',
                
            ],
            [
                'name' => 'Arif Muhammad',
                'email' => 'petugas@example.com',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'no_hp' => '081234567891',
                'alamat' => 'Jl. Contoh No. 2',
                
            ],
            [
                'name' => 'Peminjam',
                'email' => 'peminjam@example.com',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'no_hp' => '081234567892',
                'alamat' => 'Jl. Contoh No. 3',
                
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'no_hp' => '081234567893',
                'alamat' => 'Jl. Merdeka No. 10',
                
            ],
            [
                'name' => 'Siti Rahma',
                'email' => 'siti@example.com',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'no_hp' => '081234567894',
                'alamat' => 'Jl. Sudirman No. 5',
                
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
