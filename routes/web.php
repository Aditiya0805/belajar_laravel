<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Tugas 7: Kunci SEMUA rute kelola barang: hanya yang login boleh masuk.
Route::resource('barang', BarangController::class)
    ->middleware('auth');

// katalog dibaca pembeli (publik) - tetap terbuka untuk guest:
Route::get('/katalog', [BarangController::class, 'index'])->name('katalog');

Route::get('/product', [ProductController::class, 'index']);

require __DIR__.'/auth.php';
