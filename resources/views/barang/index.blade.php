@extends('layouts.app')

@section('title', 'Katalog Produk - TokoKita')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Katalog Produk</h2>
        <a href="{{ route('barang.create') }}" class="btn btn-primary">+ Tambah Produk</a>
    </div>

    {{-- Tugas 4 (P13 & P16): Komponen Alert Pesan Status / Flash Session --}}
    @if (session('sukses'))
        <x-alert-status type="success" :message="session('sukses')" />
    @else
        <x-alert-status type="success" message="Selamat datang di TokoKita!" />
    @endif

    @php
        // Jika variabel $barangs belum dipassing dari controller (Sesi 1), gunakan data dummy
        if (!isset($barangs)) {
            $barangs = [
                ['nama' => 'Kopi Arabika', 'harga' => 75000, 'stok' => 12, 'kategori' => 'Minuman'],
                ['nama' => 'Teh Melati', 'harga' => 25000, 'stok' => 0, 'kategori' => 'Minuman'],
                ['nama' => 'Gula Pasir 1kg', 'harga' => 18000, 'stok' => 30, 'kategori' => 'Bahan Pokok'],
            ];
        }
    @endphp

    <div class="row">
        {{-- TODO 3a & 13: pakai @forelse agar array / koleksi kosong bisa ditangani --}}
        @forelse($barangs as $b)
            <x-card-barang
                :nama="is_array($b) ? $b['nama'] : $b->nama"
                :harga="is_array($b) ? $b['harga'] : $b->harga"
                :stok="is_array($b) ? $b['stok'] : $b->stok"
                :kategori="is_array($b) ? ($b['kategori'] ?? '-') : ($b->kategori->nama ?? '-')"
            >
                @if(!is_array($b) && isset($b->id))
                    <a href="{{ route('barang.edit', $b->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('barang.destroy', $b->id) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                @endif
            </x-card-barang>
        @empty
            <div class="col-12">
                <div class="alert alert-warning">Belum ada produk tersedia.</div>
            </div>
        @endforelse
    </div>

    {{-- TODO 3b: tampilkan jumlah produk memakai count($barangs) --}}
    <p class="text-muted mt-3">Total produk: {{ count($barangs) }} item</p>
@endsection
