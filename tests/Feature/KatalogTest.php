<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KatalogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test migration dan seeder berjalan dengan benar (Sesi 2).
     */
    public function test_migration_and_seeder_run_successfully(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('kategoris', 3);
        $this->assertDatabaseCount('barangs', 5);

        $this->assertDatabaseHas('kategoris', [
            'nama' => 'Minuman',
        ]);

        $this->assertDatabaseHas('barangs', [
            'sku' => 'KM-001',
            'nama' => 'Kopi Arabika',
            'harga' => 75000,
            'stok' => 12,
        ]);

        $this->assertDatabaseHas('barangs', [
            'sku' => 'KM-002',
            'nama' => 'Teh Melati',
            'harga' => 25000,
            'stok' => 0,
        ]);
    }

    /**
     * Test relasi Eloquent hasMany dan belongsTo (Sesi 3 - Tugas 12).
     */
    public function test_eloquent_relationships_work(): void
    {
        $this->seed(DatabaseSeeder::class);

        $kopi = Barang::where('sku', 'KM-001')->first();
        $this->assertNotNull($kopi);
        $this->assertEquals('Minuman', $kopi->kategori->nama);

        $minuman = Kategori::where('nama', 'Minuman')->first();
        $this->assertNotNull($minuman);
        $this->assertTrue($minuman->barangs->contains('sku', 'KM-001'));
        $this->assertTrue($minuman->barangs->contains('sku', 'KM-002'));
    }

    /**
     * Test query builder dasar sesuai modul Tugas 9 via DB facade.
     */
    public function test_query_builder_queries_from_modul(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 1. Semua baris
        $all = DB::table('barangs')->get();
        $this->assertCount(5, $all);

        // 2. Stok > 0
        $inStock = DB::table('barangs')->where('stok', '>', 0)->get();
        $this->assertCount(4, $inStock);

        // 3. SKU KM-001
        $first = DB::table('barangs')->where('sku', 'KM-001')->first();
        $this->assertNotNull($first);
        $this->assertEquals('Kopi Arabika', $first->nama);

        // 4. Harga < 30000
        $cheapCount = DB::table('barangs')->where('harga', '<', 30000)->count();
        $this->assertEquals(3, $cheapCount); // Teh Melati (25000), Gula Pasir (18000), Mie Instan (3500)

        // 5. Order by harga desc limit 3
        $top3 = DB::table('barangs')->orderBy('harga', 'desc')->limit(3)->get();
        $this->assertCount(3, $top3);
        $this->assertEquals('Kopi Arabika', $top3[0]->nama); // 75000
        $this->assertEquals('Beras Pandan Wangi 5kg', $top3[1]->nama); // 72000
        $this->assertEquals('Teh Melati', $top3[2]->nama); // 25000
    }

    /**
     * Test halaman /katalog dapat dirender dengan komponen dan data relasi (Sesi 1 & 3).
     */
    public function test_katalog_page_renders_with_components_and_relations(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/katalog');

        $response->assertStatus(200);
        $response->assertViewIs('barang.index');
        $response->assertViewHas('barangs');

        // Layout induk & header
        $response->assertSee('TOKOKITA');
        $response->assertSee('Katalog Produk');
        $response->assertSee('Selamat datang di TokoKita!');

        // Kartu barang & harga format rupiah & kategori
        $response->assertSee('Kopi Arabika');
        $response->assertSee('Rp 75.000');
        $response->assertSee('Stok: 12');
        $response->assertSee('Minuman');

        // Barang stok 0 berbadge HABIS
        $response->assertSee('Teh Melati');
        $response->assertSee('Rp 25.000');
        $response->assertSee('HABIS');

        // Footer
        $response->assertSee('TokoKita - Praktikum Pemrograman Web 2');
    }

    /**
     * Test pembuktian Eager Loading vs N+1 Problem (Sesi 3 - Tugas 14).
     */
    public function test_eager_loading_prevents_n_plus_one_queries(): void
    {
        $this->seed(DatabaseSeeder::class);

        // Hitung query dengan eager loading (Barang::with('kategori')->get())
        DB::flushQueryLog();
        DB::enableQueryLog();

        $barangsWith = Barang::with('kategori')->get();
        foreach ($barangsWith as $b) {
            $kategoriNama = $b->kategori->nama;
        }

        $queryCountWith = count(DB::getQueryLog());
        // Harus hanya 2 query (1 select barangs, 1 select kategoris where id in (...))
        $this->assertEquals(2, $queryCountWith);

        // Bandingkan dengan lazy loading tanpa with() (N+1 Problem)
        DB::flushQueryLog();
        $barangsWithout = Barang::all();
        foreach ($barangsWithout as $b) {
            $kategoriNama = $b->kategori->nama;
        }

        $queryCountWithout = count(DB::getQueryLog());
        // QueryCountWithout = 1 (select all barangs) + N (select kategori per baris) = 1 + 5 = 6 query
        $this->assertEquals(6, $queryCountWithout);
    }

    /**
     * Test CRUD lengkap barang (Pertemuan 16): create, store, edit, update, destroy.
     */
    public function test_crud_barang_resource_works_completely(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create();
        $this->actingAs($user);

        $kategori = Kategori::first();

        // 1. Index resource route (/barang) menampilkan tombol tambah, edit, dan hapus
        $indexResponse = $this->get(route('barang.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Tambah Produk');
        $indexResponse->assertSee('Edit');
        $indexResponse->assertSee('Hapus');

        // 2. Halaman Create menampilkan form & dropdown kategori dinamis
        $createResponse = $this->get(route('barang.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertViewIs('barang.create');
        $createResponse->assertSee('Tambah Produk Baru');
        $createResponse->assertSee($kategori->nama);

        // 3. Store menyimpan produk baru dan redirect dengan flash message
        $storeResponse = $this->post(route('barang.store'), [
            'nama' => 'Susu UHT Full Cream',
            'sku' => 'KM-099',
            'harga' => 22000,
            'stok' => 15,
            'kategori_id' => $kategori->id,
        ]);

        $storeResponse->assertRedirect(route('barang.index'));
        $storeResponse->assertSessionHas('sukses', 'Produk berhasil ditambahkan!');
        $this->assertDatabaseHas('barangs', [
            'sku' => 'KM-099',
            'nama' => 'Susu UHT Full Cream',
            'harga' => 22000,
            'stok' => 15,
            'kategori_id' => $kategori->id,
        ]);

        $barang = Barang::where('sku', 'KM-099')->firstOrFail();

        // 4. Halaman Edit terisi data lama dan dropdown terpilih
        $editResponse = $this->get(route('barang.edit', $barang->id));
        $editResponse->assertStatus(200);
        $editResponse->assertViewIs('barang.edit');
        $editResponse->assertSee('Edit Produk: Susu UHT Full Cream');
        $editResponse->assertSee('KM-099');

        // 5. Update memperbarui data dan redirect dengan flash message
        $updateResponse = $this->put(route('barang.update', $barang->id), [
            'nama' => 'Susu UHT Cokelat',
            'sku' => 'KM-099',
            'harga' => 24000,
            'stok' => 20,
            'kategori_id' => $kategori->id,
        ]);

        $updateResponse->assertRedirect(route('barang.index'));
        $updateResponse->assertSessionHas('sukses', 'Produk berhasil diperbarui!');
        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'nama' => 'Susu UHT Cokelat',
            'harga' => 24000,
            'stok' => 20,
        ]);

        // 6. Destroy menghapus data dan redirect dengan flash message
        $destroyResponse = $this->delete(route('barang.destroy', $barang->id));
        $destroyResponse->assertRedirect(route('barang.index'));
        $destroyResponse->assertSessionHas('sukses', 'Produk berhasil dihapus!');
        $this->assertDatabaseMissing('barangs', [
            'id' => $barang->id,
        ]);
    }

    /**
     * Test validasi SKU unik mencegah error 500 UniqueConstraintViolationException saat tambah/edit.
     */
    public function test_duplicate_sku_returns_validation_error_instead_of_server_error(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create();
        $this->actingAs($user);

        $kategori = Kategori::first();

        // 1. Tambah data dengan SKU yang sudah ada ('KM-001') harus gagal validasi secara aman
        $storeResponse = $this->from(route('barang.create'))->post(route('barang.store'), [
            'nama' => 'Nasi padang',
            'sku' => 'KM-001',
            'harga' => 15000,
            'stok' => 10,
            'kategori_id' => $kategori->id,
        ]);

        $storeResponse->assertRedirect(route('barang.create'));
        $storeResponse->assertSessionHasErrors('sku');
        $this->assertDatabaseMissing('barangs', [
            'nama' => 'Nasi padang',
        ]);

        // 2. Edit produk lain dengan SKU milik produk berbeda ('KM-001') juga harus gagal validasi
        $tehMelati = Barang::where('sku', 'KM-002')->firstOrFail();

        $updateResponse = $this->from(route('barang.edit', $tehMelati->id))->put(route('barang.update', $tehMelati->id), [
            'nama' => 'Teh Melati Updated',
            'sku' => 'KM-001',
            'harga' => 25000,
            'stok' => 5,
            'kategori_id' => $kategori->id,
        ]);

        $updateResponse->assertRedirect(route('barang.edit', $tehMelati->id));
        $updateResponse->assertSessionHasErrors('sku');
    }

    /**
     * Test middleware auth melindungi rute kelola barang (Pertemuan 17 - Tugas 7).
     * Rute barang.* wajib login (guest dialihkan ke /login), sedangkan /katalog tetap publik.
     */
    public function test_auth_middleware_protects_barang_routes_but_keeps_katalog_public(): void
    {
        // 1. Guest mengakses /barang -> dialihkan ke /login
        $response = $this->get('/barang');
        $response->assertRedirect('/login');

        // 2. Guest mengakses /barang/create -> dialihkan ke /login
        $responseCreate = $this->get('/barang/create');
        $responseCreate->assertRedirect('/login');

        // 3. Guest mengakses /katalog -> tetap dapat diakses publik (status 200)
        $this->seed(DatabaseSeeder::class);
        $responseKatalog = $this->get('/katalog');
        $responseKatalog->assertStatus(200);
    }
}
