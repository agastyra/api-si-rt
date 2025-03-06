<?php

namespace Database\Seeders;

use App\Models\Penghuni;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Pak RT',
            'email' => 'rt@mail.com',
            'password' => bcrypt('rt02jaya'),
        ]);

        Penghuni::create([
            "nama_lengkap" => "Rangga Agastya",
            "foto_ktp" => "foto-ktp.png",
            "jenis_kelamin" => "Laki-laki",
            "status_penghuni" => "Tetap",
            "menikah" => false,
            "nomor_telepon" => "085607799274",
            "created_by" => 1,
            "updated_by" => 1
        ]);
    }
}
