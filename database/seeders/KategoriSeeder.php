<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategoris')->insert([
            ['nama' => 'Minuman', 'deskripsi' => 'Kopi, teh, dan minuman lain'],
            ['nama' => 'Makanan', 'deskripsi' => 'Makanan kering & instan'],
            ['nama' => 'Bahan Pokok', 'deskripsi' => 'Gula, beras, dan kebutuhan dapur'],
        ]);
    }
}
