<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun admin
        User::firstOrCreate(
            ['email' => 'admin@findercampus.com'],
            [
                'name' => 'Administrator',
                'nim_nip' => 'ADMIN001',
                'fakultas' => 'Rektorat',
                'whatsapp' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'whatsapp_visible' => true,
            ]
        );
        
        // Buat akun user biasa untuk testing
        User::firstOrCreate(
            ['email' => 'mahasiswa@findercampus.com'],
            [
                'name' => 'Budi Mahasiswa',
                'nim_nip' => '123456789',
                'fakultas' => 'Fakultas Ilmu Komputer',
                'whatsapp' => '089876543210',
                'password' => Hash::make('password'),
                'role' => 'user',
                'whatsapp_visible' => false,
            ]
        );
    }
}
