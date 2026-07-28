<?php

namespace Database\Seeders;

use App\Models\Posyandu;
use Illuminate\Database\Seeder;

class PosyanduSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // RW 01
            ['nama' => 'Melati 1', 'rw' => 'RW 01'],
            ['nama' => 'Melati 2', 'rw' => 'RW 01'],
            ['nama' => 'Melati 3', 'rw' => 'RW 01'],
            // RW 02
            ['nama' => 'Dahlia 1', 'rw' => 'RW 02'],
            ['nama' => 'Dahlia 2', 'rw' => 'RW 02'],
            ['nama' => 'Dahlia 3', 'rw' => 'RW 02'],
            // RW 03
            ['nama' => 'Cempaka 1', 'rw' => 'RW 03'],
            ['nama' => 'Cempaka 2', 'rw' => 'RW 03'],
            ['nama' => 'Cempaka 3', 'rw' => 'RW 03'],
            // RW 04
            ['nama' => 'Mawar Melati 1', 'rw' => 'RW 04'],
            ['nama' => 'Mawar Melati 2', 'rw' => 'RW 04'],
            // RW 05
            ['nama' => 'Bakti Ibu 1', 'rw' => 'RW 05'],
            ['nama' => 'Bakti Ibu 2', 'rw' => 'RW 05'],
            // RW 06
            ['nama' => 'Flamboyan 1', 'rw' => 'RW 06'],
            ['nama' => 'Flamboyan 2', 'rw' => 'RW 06'],
            ['nama' => 'Flamboyan 3', 'rw' => 'RW 06'],
            // RW 07
            ['nama' => 'Melati RW 07', 'rw' => 'RW 07'],
            // RW 08
            ['nama' => 'Kenanga 1', 'rw' => 'RW 08'],
            ['nama' => 'Kenanga 2', 'rw' => 'RW 08'],
            // RW 09
            ['nama' => 'Melati Mekar', 'rw' => 'RW 09'],
            // RW 10
            ['nama' => 'Melati RW 10', 'rw' => 'RW 10'],
        ];

        foreach ($data as $posyandu) {
            Posyandu::create($posyandu);
        }
    }
}
