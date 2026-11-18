<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'sku', 'harga', 'stok', 'kategori_id'];

    /**
     * Relasi satu barang milik satu kategori (belongsTo).
     */
    public function kategori(): BelongsTo
    {
        // TODO 12b: satu barang milik satu kategori
        return $this->belongsTo(Kategori::class);
    }
}
