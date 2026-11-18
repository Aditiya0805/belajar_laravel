<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('barangs')->insert([
            ['nama' => 'Kopi Arabika', 'sku' => 'KM-001', 'harga' => 75000, 'stok' => 12, 'kategori_id' => 1],
            ['nama' => 'Teh Melati', 'sku' => 'KM-002', 'harga' => 25000, 'stok' => 0, 'kategori_id' => 1],
            ['nama' => 'Gula Pasir 1kg', 'sku' => 'BP-001', 'harga' => 18000, 'stok' => 30, 'kategori_id' => 3],
            // TODO 8: tambahkan minimal 2 barang lagi (pilih kategori_id yang sesuai)
            ['nama' => 'Beras Pandan Wangi 5kg', 'sku' => 'BP-002', 'harga' => 72000, 'stok' => 20, 'kategori_id' => 3],
            ['nama' => 'Mie Instan Goreng', 'sku' => 'MK-001', 'harga' => 3500, 'stok' => 100, 'kategori_id' => 2],
        ]);
    }
}
