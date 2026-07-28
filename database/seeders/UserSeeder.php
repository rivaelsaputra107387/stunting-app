<?php

namespace Database\Seeders;

use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin Kelurahan
        User::create([
            'name'     => 'Admin Kelurahan Sukahaji',
            'email'    => 'admin@kelurahan.test',
            'password' => bcrypt('password'),
            'role'     => 'kelurahan',
            'posyandu_id' => null,
        ]);

        // 2. One account per Posyandu (21 accounts)
        $posyandus = Posyandu::all();

        foreach ($posyandus as $posyandu) {
            $slug = Str::slug($posyandu->nama, '.');
            User::create([
                'name'     => 'Petugas ' . $posyandu->nama,
                'email'    => "posyandu.{$slug}@test.com",
                'password' => bcrypt('password'),
                'role'     => 'posyandu',
                'posyandu_id' => $posyandu->id,
            ]);
        }
    }
}
