# LAPORAN JAWABAN PRAKTIKUM INTEGRATIF
**Mata Kuliah:** Pemrograman Web 2 (INF231102)  
**Pertemuan:** 13–15 (Blade, Migration & Eloquent ORM - Studi Kasus Tokokita)  
**Mahasiswa:** Aditiya Pratama  
**Dosen Pengampu:** Reflan Nuari, S.Kom., M.Kom.  

---

## SESI 1 - BLADE: LAYOUT, KOMPONEN & DIREKTIF (Pertemuan 13)

### Tugas 1 - Layout Induk (`resources/views/layouts/app.blade.php`)
* **Slot Konten Utama:** Didefinisikan menggunakan `@yield('content')`.
* **Judul Halaman Dinamis:** Didefinisikan menggunakan `@yield('title', 'TokoKita')`.
* **Framework CSS:** Bootstrap 5.3 via CDN.

#### Pertanyaan: Beda `@yield` dan `@section` dari Sudut Pandang Parent vs Child
* **`@yield` (Parent View):**
  Digunakan pada file layout induk (*parent layout*, misalnya `layouts/app.blade.php`) sebagai penanda tempat (*placeholder* atau slot kosong) untuk mendefinisikan area di mana konten dinamis dari halaman turunan akan disuntikkan.
* **`@section` (Child View):**
  Digunakan pada file tampilan anak (*child view*, misalnya `barang/index.blade.php`) untuk membungkus konten nyata yang akan disalurkan dan dimasukkan ke dalam slot `@yield` yang sesuai di file parent layout.

---


### Tugas 2 - Komponen Kartu Produk (`resources/views/components/card-barang.blade.php`)
* Komponen menerima props: `@props(['nama', 'harga', 'stok' => 0, 'kategori' => '-'])`.
* Format harga Rupiah: `Rp {{ number_format($harga, 0, ',', '.') }}`.
* Badge stok dinamis: Berwarna hijau (`bg-success`) dengan label `Stok: {stok}` bila stok > 0, dan berwarna merah (`bg-danger`) dengan label `HABIS` bila stok == 0.

---

### Tugas 3 - Daftar Produk Katalog (`resources/views/barang/index.blade.php`)

#### Pertanyaan: Keuntungan `@forelse` dibanding `@foreach` saat Array Kosong
* Pada perulangan `@foreach`, apabila data array atau koleksi bernilai kosong (`empty`), template membutuhkan pengkondisian manual tambahan berupa `if (!empty($barangs)) ... else ... endif`.
* Sedangkan pada direktif `@forelse`, Blade sudah menyediakan blok alternatif `@empty ... @endforelse` secara otomatis dan terintegrasi. Hal ini membuat struktur kode jauh lebih ringkas, mudah dibaca (*clean code*), serta mencegah potensi error atau tampilan kosong tanpa pemberitahuan kepada pengguna.

---

### Tugas 4 - Komponen Alert Pesan Status (`resources/views/components/alert-status.blade.php`)
* Menggunakan Bootstrap dismissible alert: `<div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">`.
* Menampilkan pesan dinamis dengan tombol tutup (`btn-close`).
* Diintegrasikan pada view katalog: `<x-alert-status type="success" message="Selamat datang di TokoKita!" />`.

---

## SESI 2 - MIGRATION, SEEDER & QUERY BUILDER (Pertemuan 14)

### Tugas 6 - Migration Tabel `kategoris` dan `barangs`

#### Pertanyaan 6a: Kenapa kolom `nama` di tabel `kategoris` diberi constraint `->unique()`?
* **Jawaban:** Diberi constraint `unique()` untuk memastikan setiap kategori hanya terdaftar satu kali dan tidak ada duplikasi data kategori (contoh: mencegah ada dua kategori bernama "Minuman"). Hal ini krusial untuk menjaga integritas dan konsistensi data.

#### Pertanyaan 6b: Kenapa kolom `deskripsi` di tabel `kategoris` diberi `->nullable()`?
* **Jawaban:** Diberi `nullable()` karena informasi keterangan atau deskripsi kategori bersifat opsional. Tidak semua kategori membutuhkan penjelasan panjang, sehingga diperbolehkan bernilai `NULL` di database.

#### Pertanyaan 6c: Apa yang terjadi bila method `down()` tidak ditulis / dibiarkan kosong?
* **Jawaban:** Jika method `down()` tidak ditulis (kosong), perintah pembatalan migrasi seperti `php artisan migrate:rollback`, `php artisan migrate:reset`, atau `php artisan migrate:refresh` tidak akan menjalankan operasi apa pun (tabel tidak akan di-drop). Akibatnya, perubahan skema database tidak dapat dibatalkan secara otomatis jika terjadi kesalahan.

---

### Tugas 7 - Jalankan & Verifikasi Migrate
* Perintah eksekusi migrasi:
  ```bash
  php artisan migrate
  php artisan migrate:status
  ```
* **Hasil Eksperimen Rollback:**
  Saat menjalankan `php artisan migrate:rollback`, tabel `barangs` dan `kategoris` berhasil dihapus secara berurutan sesuai relasi foreign key (tabel anak `barangs` di-drop terlebih dahulu, kemudian tabel induk `kategoris`). Menjalankan `php artisan migrate` kembali membuat kedua tabel tersebut secara sempurna tanpa error.

---

### Tugas 8 - Seeder & Data Awal (`KategoriSeeder` & `BarangSeeder`)

#### Pertanyaan: Beda `php artisan db:seed` dan `php artisan migrate:fresh --seed`
* **`php artisan db:seed`:**
  Hanya mengeksekusi kelas-kelas Seeder untuk memasukkan (*insert*) data contoh ke dalam tabel yang sudah ada. Struktur dan isi tabel sebelumnya tidak dihapus, sehingga bila dijalankan berulang dapat menimbulkan duplikasi data atau error *unique constraint*.
* **`php artisan migrate:fresh --seed`:**
  Menghapus (*drop*) seluruh tabel yang ada di dalam database terlebih dahulu, kemudian menjalankan ulang semua file migrasi dari awal (*fresh*), dan terakhir menjalankan seeder. Perintah ini membersihkan database total dan mengisinya kembali dari nol.

#### Data yang Diinputkan via Seeder:
1. **Kategori:**
   * ID 1: Minuman (Kopi, teh, dan minuman lain)
   * ID 2: Makanan (Makanan kering & instan)
   * ID 3: Bahan Pokok (Gula, beras, dan kebutuhan dapur)
2. **Barang (5 item):**
   * KM-001 | Kopi Arabika | Rp 75.000 | Stok: 12 | Kategori: Minuman
   * KM-002 | Teh Melati | Rp 25.000 | Stok: 0 | Kategori: Minuman
   * BP-001 | Gula Pasir 1kg | Rp 18.000 | Stok: 30 | Kategori: Bahan Pokok
   * BP-002 | Beras Pandan Wangi 5kg | Rp 72.000 | Stok: 20 | Kategori: Bahan Pokok
   * MK-001 | Mie Instan Goreng | Rp 3.500 | Stok: 100 | Kategori: Makanan

---

### Tugas 9 - Query Builder Dasar via Tinker

Output hasil pengujian query:

```php
use Illuminate\Support\Facades\DB;

// 1. Mengambil semua baris:
DB::table('barangs')->get();
// Output: Collection of 5 objects (Kopi Arabika, Teh Melati, Gula Pasir 1kg, Beras Pandan Wangi 5kg, Mie Instan Goreng)

// 2. Filter barang dengan stok > 0:
DB::table('barangs')->where('stok', '>', 0)->get();
// Output: Collection of 4 objects (Kopi Arabika, Gula Pasir 1kg, Beras Pandan Wangi 5kg, Mie Instan Goreng). 'Teh Melati' tidak muncul karena stok = 0.

// 3. Mengambil barang pertama dengan SKU KM-001:
DB::table('barangs')->where('sku', 'KM-001')->first();
// Output: Objek stdClass tunggal: {"id":1, "nama":"Kopi Arabika", "sku":"KM-001", "harga":75000, "stok":12, "kategori_id":1}

// 4. Menghitung jumlah barang dengan harga < 30000:
DB::table('barangs')->where('harga', '<', 30000)->count();
// Output: 3 (Teh Melati Rp 25.000, Gula Pasir Rp 18.000, Mie Instan Rp 3.500)

// 5. Mengambil 3 produk termahal:
DB::table('barangs')->orderBy('harga', 'desc')->limit(3)->get();
// Output: Collection of 3 objects (Kopi Arabika Rp 75.000, Beras Pandan Wangi Rp 72.000, Teh Melati Rp 25.000)
```

#### Penjelasan Perbedaan `->get()`, `->first()`, dan `->count()`:
* **`->get()`:** Mengeksekusi query `SELECT *` dan mengembalikan seluruh baris yang cocok dalam bentuk `Illuminate\Support\Collection` berisi objek. Digunakan jika ingin menampilkan banyak record.
* **`->first()`:** Mengeksekusi query dengan limit 1 dan hanya mengembalikan **satu baris pertama** sebagai objek tunggal (atau `null` jika tidak ada). Cocok untuk pencarian unik seperti berdasarkan ID atau SKU.
* **`->count()`:** Menjalankan fungsi agregat SQL `SELECT count(*)`, dan mengembalikan sebuah angka integer total baris yang cocok tanpa mengambil data baris ke memori, sehingga sangat hemat performa.

---

## SESI 3 - ELOQUENT ORM & RELASI ANTAR TABEL (Pertemuan 15)

### Tugas 11 - Model & Mass Assignment (`Kategori.php` & `Barang.php`)

#### Pertanyaan: Apa risiko bila `$fillable` dikosongkan semua (atau memakai `$guarded = []`)?
* **Jawaban:** Berisiko terkena celah keamanan **Mass Assignment Vulnerability**. Pengguna jahat dapat memanipulasi parameter form/JSON HTTP request untuk mengubah kolom sensitif database yang seharusnya tidak boleh diubah oleh sembarang pengguna (misalnya mengubah hak akses `is_admin`, `role`, atau mengganti `user_id`/`kategori_id`). Properti `$fillable` bertindak sebagai *whitelist* kolom mana saja yang aman diisi secara massal.

---

### Tugas 12 - Relasi Eloquent `hasMany` dan `belongsTo`
* **`Kategori.php`:**
  ```php
  public function barangs(): HasMany
  {
      return $this->hasMany(Barang::class);
  }
  ```
* **`Barang.php`:**
  ```php
  public function kategori(): BelongsTo
  {
      return $this->belongsTo(Kategori::class);
  }
  ```

#### Pertanyaan: Bagaimana Laravel tahu kolom penghubungnya?
* **Jawaban:** Laravel menggunakan prinsip *Convention over Configuration*. Secara default, ketika relasi `belongsTo(Kategori::class)` dipanggil, Laravel akan mengambil nama fungsi relasinya (`kategori`) lalu menambahkan akhiran `_id` sehingga menjadi `kategori_id`. Laravel mencocokkan `barangs.kategori_id` dengan primary key `kategoris.id`.

---

### Tugas 13 - Controller & Route Katalog
* **Controller:** `app/Http/Controllers/BarangController.php`
  ```php
  public function index(): View
  {
      $barangs = Barang::with('kategori')->get();
      return view('barang.index', compact('barangs'));
  }
  ```
* **Route:** `routes/web.php`
  ```php
  Route::get('/katalog', [BarangController::class, 'index'])->name('katalog');
  ```
* **Komponen:** Kartu produk menampilkan nama kategori di atas harga:
  `<small class="text-muted d-block mb-2">{{ $kategori }}</small>`.

---

### Tugas 14 - Eager Loading & N+1 Problem

#### Penjelasan N+1 Problem dengan Bahasa Sendiri:
N+1 Problem adalah masalah inefisiensi performa di mana aplikasi mengeksekusi 1 query awal untuk mengambil sekumpulan data (misalnya N buah barang), kemudian di dalam perulangan (*loop*), aplikasi mengeksekusi 1 query tambahan secara terpisah untuk setiap item demi mengambil data relasinya (misalnya kategori barang tersebut). Akibatnya, total query yang dieksekusi menjadi `1 + N`. Bila ada 1000 barang, aplikasi akan mengirimkan 1001 query terpisah ke database server, yang menyebabkan latensi tinggi dan beban server berat.

**Solusi:** Eager Loading menggunakan metode `with('kategori')`.

#### Jumlah Query Hasil Eksperimen:
* **Tanpa Eager Loading (`Barang::all()` + akses `$b->kategori->nama`):**
  * Jumlah query: **6 query** ($1 + 5 = 6$)
  * Rincian:
    1. `select * from "barangs"`
    2. `select * from "kategoris" where "kategoris"."id" = 1 limit 1`
    3. `select * from "kategoris" where "kategoris"."id" = 1 limit 1`
    4. `select * from "kategoris" where "kategoris"."id" = 3 limit 1`
    5. `select * from "kategoris" where "kategoris"."id" = 3 limit 1`
    6. `select * from "kategoris" where "kategoris"."id" = 2 limit 1`
* **Dengan Eager Loading (`Barang::with('kategori')->get()`):**
  * Jumlah query: **2 query**
  * Rincian:
    1. `select * from "barangs"`
    2. `select * from "kategoris" where "kategoris"."id" in (1, 2, 3)`
  * Jumlah query tetap 2 berapa pun banyaknya data barang!

---

## PERTEMUAN 16 - CRUD LENGKAP TOKOKITA

### Tugas 1 - `Route::resource` (`routes/web.php`)
Hasil verifikasi perintah `php artisan route:list --path=barang`:

| No | Method HTTP | URI | Nama Rute (`Name`) | Aksi (`Action`) |
| :--- | :--- | :--- | :--- | :--- |
| 1 | `GET\|HEAD` | `barang` | `barang.index` | `BarangController@index` |
| 2 | `POST` | `barang` | `barang.store` | `BarangController@store` |
| 3 | `GET\|HEAD` | `barang/create` | `barang.create` | `BarangController@create` |
| 4 | `GET\|HEAD` | `barang/{barang}` | `barang.show` | `BarangController@show` |
| 5 | `PUT\|PATCH` | `barang/{barang}` | `barang.update` | `BarangController@update` |
| 6 | `DELETE` | `barang/{barang}` | `barang.destroy` | `BarangController@destroy` |
| 7 | `GET\|HEAD` | `barang/{barang}/edit` | `barang.edit` | `BarangController@edit` |

#### Pertanyaan: Rute mana saja yang TIDAK perlu dipakai karena kita tidak membuat halaman detail?
* **Jawaban:** Rute **`barang.show`** (`GET barang/{barang}` -> `BarangController@show`), karena seluruh detail produk (nama, kategori, harga, dan stok) sudah langsung ditampilkan pada kartu di halaman daftar katalog (`barang.index`).

---

### Tugas 3 - Form Create + `@csrf` (`resources/views/barang/create.blade.php`)

#### Pembuktian & Penjelasan Jika `@csrf` Dihapus:
* Jika direktif `@csrf` di dalam `<form method="POST">` dihapus dan form dikirim (*submit*), Laravel akan menolak permintaan tersebut dan menampilkan halaman error **`419 | Page Expired`** (CSRF Token Mismatch). Hal ini terjadi karena middleware keamanan bawaan Laravel (`VerifyCsrfToken`) mewajibkan setiap request pengubah data (`POST`, `PUT`, `PATCH`, `DELETE`) menyertakan token CSRF yang valid untuk mencegah serangan *Cross-Site Request Forgery*.

---

### Tugas 4 - `store()`: Simpan Produk (`BarangController@store`)

#### Pertanyaan: Apa yang terjadi bila `$fillable` pada model `Barang` tidak mencakup `'sku'`?
* **Jawaban:** Saat `Barang::create($request->all())` dijalankan, Eloquent akan menyaring input berdasarkan daftar whitelist `$fillable`. Jika `'sku'` tidak didaftarkan di `$fillable`, nilai input `sku` dari form akan diabaikan/tertolak (*mass-assignment filtering*), sehingga kolom `sku` tidak ikut tersimpan atau menjadi kosong. Di level database MySQL, ini akan memicu error query SQL (`SQLSTATE[HY000]: General error: 1364 Field 'sku' doesn't have a default value`) karena kolom `sku` bersifat wajib (*NOT NULL*) tanpa nilai *default*.

---

### Tugas 5 - Dropdown Kategori Dinamis

#### Pertanyaan: Mengapa data dropdown diambil di controller, bukan di-query langsung di dalam Blade?
* **Jawaban:** Mengikuti prinsip arsitektur **MVC (*Model-View-Controller*)** dan *Separation of Concerns*:
  1. **Controller** bertanggung jawab mengambil data dari Model dan mempersiapkannya.
  2. **View (Blade)** bertugas murni untuk menampilkan UI/HTML dari data yang dikirim Controller.
  Meng-query database langsung di dalam file `.blade.php` membuat kode berantakan, sulit diuji (*testable*), dan melanggar pemisahan tanggung jawab MVC.

---

### Tugas 6 - Edit & Update (`resources/views/barang/edit.blade.php` & `BarangController@update`)

#### Pembuktian Alur Jika `@method('PUT')` Dihapus:
* Secara standar, elemen `<form>` pada HTML hanya mendukung metode `GET` dan `POST` (tidak mengenali `PUT` atau `DELETE`).
* Laravel menyediakan fitur *method spoofing* melalui `@method('PUT')` yang menghasilkan input tersembunyi `<input type="hidden" name="_method" value="PUT">`.
* Jika `@method('PUT')` dihapus dari form edit, browser akan mengirim request murni sebagai **`POST`** ke URL `/barang/{id}`. Laravel akan menolak request tersebut dengan error **`405 Method Not Allowed`** (`The POST method is not supported for route barang/{barang}. Supported methods: GET, HEAD, PUT, PATCH, DELETE.`), karena rute `barang.update` hanya menerima `PUT` atau `PATCH`.

---

### Tugas 7 - Delete + Konfirmasi

#### Pertanyaan: Kenapa hapus lewat link `GET` biasa berbahaya?
* **Jawaban:** Karena HTTP `GET` dirancang untuk operasi pembacaan data (*read-only* dan *idempotent*). Jika penghapusan data dibuat menggunakan link `<a href="/barang/hapus/1">`:
  1. **Crawler / Bot & Link Prefetch Browser:** Bot mesin pencari atau fitur *prefetching* browser yang otomatis membuka setiap link di halaman bisa menghapus seluruh data produk secara tidak sengaja tanpa interaksi pengguna.
  2. **Rentan CSRF:** Request `GET` tidak melewati validasi token `@csrf` seperti form `POST`/`DELETE`, sehingga pihak ketiga bisa menyisipkan URL hapus tersebut di tag gambar `<img src="...">` atau link jebakan.

---

### Tugas 8 - Refleksi Tertulis

1. **Alur Lengkap Satu Operasi CRUD (Simpan Produk):**
   * Pengguna membuka `/barang/create` (`BarangController@create`), lalu Controller mengambil semua kategori (`Kategori::all()`) dan merender view `barang/create.blade.php`.
   * Pengguna mengisi form (Nama, SKU, Harga, Stok, Kategori) lalu mengklik tombol **Simpan Produk**.
   * Browser mengirim HTTP `POST` ke route `barang.store` (`/barang`) beserta token `@csrf`.
   * Middleware Laravel memverifikasi token CSRF, lalu meneruskan request ke `BarangController@store(Request $request)`.
   * Controller mengambil input dengan `$request->all()` dan menyimpannya ke tabel `barangs` menggunakan `Barang::create($data)` (disaring oleh `$fillable` pada model `Barang`).
   * Controller mengembalikan redirect ke `route('barang.index')` dengan membawa *flash session* `'sukses' => 'Produk berhasil ditambahkan!'`.
   * Browser diarahkan ulang ke `/barang` (`BarangController@index`), Controller mengambil daftar barang beserta relasinya `Barang::with('kategori')->get()`, lalu `barang/index.blade.php` merender kartu produk baru sekaligus menampilkan komponen `<x-alert-status>` dengan pesan sukses tersebut.

2. **Fungsi `@csrf`, `@method('PUT')`, dan `@method('DELETE')` di Form HTML:**
   * **`@csrf`:** Menghasilkan *hidden input* `_token` berisi token unik sesi pengguna untuk melindungi aplikasi dari serangan *Cross-Site Request Forgery*. Wajib ada pada form `POST`, `PUT`, `PATCH`, dan `DELETE`.
   * **`@method('PUT')` dan `@method('DELETE')`:** Karena tag `<form>` HTML standar hanya mendukung `GET` dan `POST`, direktif ini melakukan *method spoofing* (menghasilkan `<input type="hidden" name="_method" value="PUT/DELETE">`) agar router Laravel mengenali bahwa request tersebut ditujukan untuk rute `update` (`PUT`) atau `destroy` (`DELETE`).

3. **Titik Penggunaan Relasi Eloquent (P15) dalam CRUD Ini:**
   * **Titik 1 (`BarangController@index` & `barang/index.blade.php`):** Menggunakan `Barang::with('kategori')->get()` untuk mengambil data relasi kategori setiap barang secara efisien (*eager loading*), lalu memanggil `$b->kategori->nama` untuk menampilkan nama kategori pada komponen `<x-card-barang>`.
   * **Titik 2 (`BarangController@create` / `edit` & penyimpanan `kategori_id`):** Menggunakan Model `Kategori::all()` untuk mengisi pilihan dropdown kategori (`value="{{ $k->id }}"`), dan pada model `Barang` relasi `belongsTo(Kategori::class)` terhubung melalui foreign key `kategori_id` yang masuk dalam `$fillable` saat `create()` dan `update()`.

4. **Bagian yang Paling Sulit & Cara Menyelesaikannya:**
   * Bagian yang cukup menantang adalah memastikan keterhubungan antara view komponen (`x-card-barang` yang menggunakan slot) dengan tombol Edit dan form Hapus agar tampil rapi tanpa merusak struktur Pertemuan 13–15, serta memastikan sinkronisasi koneksi database lokal (`DB_DATABASE`). Masalah tersebut diselesaikan dengan memanfaatkan fitur `$slot` pada komponen Blade `card-barang.blade.php` dan memverifikasi alur CRUD secara menyeluruh melalui automated feature testing (`KatalogTest.php`).

---

## DAFTAR FILE DAN STRUKTUR YANG DIBUAT
1. `resources/views/layouts/app.blade.php` (Layout Induk)
2. `resources/views/components/card-barang.blade.php` (Komponen Kartu Produk)
3. `resources/views/components/alert-status.blade.php` (Komponen Alert)
4. `resources/views/barang/index.blade.php` (Halaman Katalog Produk + Tombol Tambah, Edit, Hapus)
5. `resources/views/barang/create.blade.php` (Form Tambah Produk + Dropdown Kategori Dinamis)
6. `resources/views/barang/edit.blade.php` (Form Edit Produk + `@method('PUT')`)
7. `database/migrations/*_create_kategoris_table.php` (Migrasi Tabel Kategoris)
8. `database/migrations/*_create_barangs_table.php` (Migrasi Tabel Barangs)
9. `database/seeders/KategoriSeeder.php` (Seeder Kategori)
10. `database/seeders/BarangSeeder.php` (Seeder Barang)
11. `database/seeders/DatabaseSeeder.php` (Registrasi Seeder)
12. `app/Models/Kategori.php` (Model Kategori & Relasi `hasMany`)
13. `app/Models/Barang.php` (Model Barang & Relasi `belongsTo`)
14. `app/Http/Controllers/BarangController.php` (Resource Controller CRUD Lengkap)
15. `app/Providers/AppServiceProvider.php` (Query Logger)
16. `routes/web.php` (`Route::resource('barang', ...)` & `/katalog`)
17. `tests/Feature/KatalogTest.php` (Unit/Feature Test Otomatis)
18. `jawaban-praktikum.md` (Laporan Lengkap & Jawaban Soal Modul P13–P17)
19. `app/Http/Requests/StoreBarangRequest.php` (Form Request Validasi Produk)
20. `routes/auth.php` & `resources/views/auth/*` (Autentikasi Laravel Breeze)

---

## PERTEMUAN 17 - VALIDASI INPUT & AUTENTIKASI

### Tugas 1 - Validasi di `store()` (`BarangController@store`)
* Aturan validasi:
  * `nama` => `['required', 'string', 'max:100']`
  * `sku` => `['required', 'max:30', 'unique:barangs']`
  * `harga` => `['required', 'integer', 'min:0']`
  * `stok` => `['required', 'integer', 'min:0']`
  * `kategori_id` => `['required', 'exists:kategoris,id']`

#### Pertanyaan: Apa yang dilakukan Laravel otomatis saat `validate()` gagal?
* **Jawaban:** Laravel akan menghentikan eksekusi method controller dan melempar `ValidationException`. Secara otomatis, Laravel melakukan pengalihan kembali (*redirect back*) ke halaman form sebelumnya dengan membawa input lama pengguna ke dalam flash session (tersedia melalui helper `old()`) dan menyertakan daftar pesan kesalahan validasi ke dalam variabel view `$errors`.

#### Pertanyaan: Kenapa validasi browser (`required` di input HTML) tidak cukup aman?
* **Jawaban:** Validasi HTML5 di sisi browser (*client-side*) mudah dimanipulasi atau dimatikan oleh pengguna (misalnya melalui Inspect Element / DevTools dengan menghapus atribut `required`, menonaktifkan JavaScript, atau mengirim data langsung via API tools seperti Postman dan cURL). Validasi *server-side* adalah satu-satunya lapisan yang mutlak menjamin bahwa data yang masuk ke database benar-benar valid, aman, dan berintegritas.

---

### Tugas 2 - Tampilkan Error & `old()` (`resources/views/barang/create.blade.php`)
* Kotak pesan error ditampilkan di bagian atas form hanya ketika validasi gagal:
  ```blade
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul class="mb-0">
              @foreach ($errors->all() as $e)
                  <li>{{ $e }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  ```
* Nilai form dipertahankan menggunakan `value="{{ old('field') }}"`.

#### Pertanyaan: Apa beda `old('nama')` dan `old('nama', $barang->nama)`?
* **Jawaban:**
  * `old('nama')`: Digunakan pada mode **Create** (tambah data baru). Jika validasi gagal, fungsi ini mengambil data lama yang baru saja diketik. Jika form baru pertama kali dibuka, nilainya adalah `null` / kosong.
  * `old('nama', $barang->nama)`: Digunakan pada mode **Edit**. Jika validasi gagal, fungsi ini mengembalikan input terakhir pengguna. Namun jika form baru pertama kali dibuka (belum pernah ada submit gagal), fungsi ini menggunakan nilai dari database (`$barang->nama`) sebagai nilai *default*-nya.

---

### Tugas 3 - Validasi `update()` + `unique` Aman (`BarangController@update` & `edit.blade.php`)

#### Pertanyaan: Apa fungsi `->ignore($barang->id)` dan apa yang terjadi tanpa itu?
* **Jawaban:**
  * **Fungsi:** `Rule::unique('barangs')->ignore($barang->id)` memberitahu validasi database untuk mengecualikan baris record milik produk yang sedang diedit berdasarkan ID-nya saat memeriksa keunikan kolom `sku`.
  * **Tanpa `->ignore()`:** Ketika pengguna mengedit produk tanpa mengubah SKU (misalnya hanya memperbarui harga atau stok), aturan `unique` akan mendeteksi bahwa SKU tersebut sudah ada di tabel `barangs` (padahal milik produk itu sendiri), sehingga pembaruan akan selalu gagal dengan pesan error *"SKU ini sudah dipakai produk lain."*

---

### Tugas 4 - Pesan Error Bahasa Indonesia
* Diterapkan pesan kustom per aturan dan alias field:
  ```php
  [
      'nama.required' => 'Nama produk wajib diisi.',
      'nama.max' => 'Nama produk maksimal 100 karakter.',
      'sku.required' => 'SKU wajib diisi.',
      'sku.unique' => 'SKU ini sudah dipakai produk lain.',
      'harga.required' => 'Harga wajib diisi.',
      'harga.integer' => 'Harga harus berupa angka.',
      'harga.min' => 'Harga tidak boleh negatif.',
      'stok.required' => 'Stok wajib diisi.',
      'stok.integer' => 'Stok harus berupa angka.',
      'stok.min' => 'Stok tidak boleh negatif.',
      'kategori_id.required' => 'Kategori wajib dipilih.',
      'kategori_id.exists' => 'Kategori yang dipilih tidak tersedia.',
  ]
  ```

#### Pertanyaan: Apa fungsi argumen ketiga (alias field)?
* **Jawaban:** Argumen ketiga pada `validate()` berfungsi mendefinisikan *custom attribute names* (nama alias atribut). Tujuannya untuk mengganti nama kolom teknis database (misalnya `kategori_id`) menjadi nama yang ramah pengguna (misalnya `Kategori`) pada template pesan error bawaan Laravel.

---

### Tugas 5 - Form Request `StoreBarangRequest` (`app/Http/Requests/StoreBarangRequest.php`)
* Kelas Form Request dibuat menggunakan `php artisan make:request StoreBarangRequest`.
* Method controller `store()` menerima `StoreBarangRequest $request` dan mengambil data yang lolos validasi dengan `$request->validated()`.

#### Pertanyaan: Apa beda `$request->all()` dan `$request->validated()`?
* **Jawaban:**
  * `$request->all()`: Mengambil **semua** parameter input yang dikirimkan oleh browser/client ke server, termasuk input yang tidak divalidasi maupun parameter berbahaya yang disisipkan oleh penyerang.
  * `$request->validated()`: **Hanya** mengambil data yang didefinisikan dalam aturan `rules()` pada Form Request dan telah berhasil lolos validasi. Semua field lain di luar aturan akan disaring/dibuang secara otomatis, sehingga mencegah kerentanan manipulasi *mass assignment*.

---

### Tugas 6 - Pasang Laravel Breeze (Autentikasi)
* Scaffolding autentikasi dipasang menggunakan:
  ```bash
  composer require laravel/breeze --dev
  php artisan breeze:install blade
  npm install && npm run build
  php artisan migrate
  ```

#### Pertanyaan: File & folder apa saja yang dibuat oleh Breeze?
* **Jawaban:**
  1. `routes/auth.php`: Seluruh rute autentikasi (`/register`, `/login`, `/logout`, `/forgot-password`, `/reset-password`, `/verify-email`).
  2. `app/Http/Controllers/Auth/*`: Kumpulan controller autentikasi siap pakai.
  3. `app/Http/Requests/Auth/*`: Request class seperti `LoginRequest`.
  4. `resources/views/auth/*`: Tampilan Blade untuk antarmuka login, register, dll.
  5. `resources/views/layouts/navigation.blade.php`, `guest.blade.php`, dan dashboard view.

---

### Tugas 7 & 8 - Middleware `auth` Melindungi Rute
* Seluruh rute kelola barang dilindungi middleware auth di `routes/web.php`:
  ```php
  Route::resource('barang', BarangController::class)
      ->middleware('auth');

  Route::get('/katalog', [BarangController::class, 'index'])->name('katalog');
  ```

#### Pertanyaan: Middleware `auth` bekerja di lapisan mana?
* **Jawaban:** Middleware `auth` bekerja di lapisan **HTTP Middleware (Pipeline Request)**, yaitu tepat setelah request diterima oleh router dan **sebelum** method controller (`BarangController`) dijalankan. Jika pengunjung belum login, request akan langsung dihentikan dan dialihkan ke rute `/login`.

---

### Tugas 9 - Refleksi Tertulis

1. **Perbedaan Validasi Server-Side dan Validasi HTML5; Mengapa Keduanya Perlu?**
   * **Validasi HTML5:** Bekerja di sisi browser untuk memberikan pengalaman pengguna (*UX*) yang responsif tanpa perlu menunggu komunikasi jaringan ke server.
   * **Validasi Server-Side:** Bekerja di sisi server untuk menjamin keamanan dan integritas data secara mutlak.
   * **Mengapa Keduanya Perlu:** Keduanya saling melengkapi. HTML5 memberikan *instant feedback* agar pengguna nyaman, sedangkan server-side memastikan data yang disimpan ke database tetap valid dan aman meskipun ada pengguna yang mematikan atau memanipulasi HTML5 browser.

2. **Perbedaan AUTENTIKASI dan OTORISASI Beserta Contoh di Tokokita:**
   * **Autentikasi (*Authentication*):** Proses verifikasi identitas *"Siapakah pengguna ini?"*. Contoh di Tokokita: Middleware `auth` memastikan hanya pengguna terdaftar yang sudah login dengan email dan password yang dapat mengakses `/barang`.
   * **Otorisasi (*Authorization*):** Proses verifikasi hak akses *"Apa yang boleh dilakukan pengguna ini?"*. Contoh di Tokokita: Pembatasan hak akses di mana hanya pengguna dengan peran *Admin* yang boleh menghapus produk (`destroy`), sedangkan pengguna biasa hanya boleh melihat dan mengedit produk miliknya sendiri.

3. **Mengapa Breeze Lebih Aman daripada Menulis Sendiri Sistem Login dari Nol?**
   * Laravel Breeze dikembangkan dan diaudit oleh tim inti Laravel serta komunitas keamanan global. Breeze secara otomatis menyertakan praktik keamanan standar industri, seperti:
     * Hashing password dengan algoritma `bcrypt` yang kuat.
     * Regenerasi session ID untuk mencegah serangan *Session Fixation*.
     * Proteksi token CSRF pada seluruh form autentikasi.
     * Fitur *Rate Limiting / Throttling* untuk menahan serangan *Brute Force* login.
     * Penanganan token reset password yang aman dan memiliki batas kedaluwarsa.
     Menulis login sendiri dari nol sangat berisiko melupakan salah satu dari mekanisme pertahanan penting ini.

4. **Setelah Modul Ini, Bagian Mana dari Tokokita yang Masih Rentan?**
   * Tokokita masih rentan pada aspek **Otorisasi tingkat objek/data (*Object-Level Authorization / Ownership*)**. Saat ini, siapa pun pengguna yang telah login memiliki hak yang sama untuk mengedit atau menghapus barang apa pun di database, meskipun barang tersebut ditambahkan oleh pengguna lain. Belum ada relasi kepemilikan (`user_id` pada tabel `barangs`) dan belum diterapkan Laravel Policy / Gate untuk memverifikasi hak kepemilikan sebelum operasi `update` atau `delete` dilakukan.

