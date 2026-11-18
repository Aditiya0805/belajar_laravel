<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;

    // TODO 11: tulis juga $fillable untuk model Kategori
    protected $fillable = ['nama', 'deskripsi'];

    /**
     * Relasi satu kategori memiliki banyak barang (one-to-many).
     */
    public function barangs(): HasMany
    {
        // TODO 12a: satu kategori punya banyak barang
        return $this->hasMany(Barang::class);
    }
}
