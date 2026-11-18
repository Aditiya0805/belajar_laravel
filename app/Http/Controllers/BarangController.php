<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBarangRequest;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    /**
     * Menampilkan katalog produk beserta relasi kategori.
     */
    public function index(): View
    {
        $barangs = Barang::with('kategori')->get();

        return view('barang.index', compact('barangs'));
    }

    /**
     * Menampilkan form tambah produk baru dengan dropdown kategori dinamis.
     */
    public function create(): View
    {
        $kategoris = Kategori::all();

        return view('barang.create', compact('kategoris'));
    }

    /**
     * Menyimpan produk baru ke database menggunakan Form Request (Tugas 5).
     */
    public function store(StoreBarangRequest $request): RedirectResponse
    {
        $data = $request->validated(); // HANYA field yang lolos aturan
        Barang::create($data);

        return redirect()->route('barang.index')
            ->with('sukses', 'Produk berhasil ditambahkan!');
    }

    /**
     * Redirect ke halaman utama katalog karena halaman detail tidak dibuat.
     */
    public function show(Barang $barang): RedirectResponse
    {
        return redirect()->route('barang.index');
    }

    /**
     * Menampilkan form edit produk beserta dropdown kategori.
     */
    public function edit(Barang $barang): View
    {
        $kategoris = Kategori::all();

        return view('barang.edit', compact('barang', 'kategoris'));
    }

    /**
     * Memperbarui data produk yang dipilih di database (Tugas 3 & 4).
     */
    public function update(Request $request, Barang $barang): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'sku' => [
                'required',
                'max:30',
                Rule::unique('barangs')->ignore($barang->id),
            ],
            'harga' => ['required', 'integer', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
        ], [
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
        ], [
            'nama' => 'Nama Produk',
            'sku' => 'SKU',
            'kategori_id' => 'Kategori',
        ]);

        $barang->update($data);

        return redirect()->route('barang.index')
            ->with('sukses', 'Produk berhasil diperbarui!');
    }

    /**
     * Menghapus data produk dari database.
     */
    public function destroy(Barang $barang): RedirectResponse
    {
        $barang->delete();

        return redirect()->route('barang.index')
            ->with('sukses', 'Produk berhasil dihapus!');
    }
}
