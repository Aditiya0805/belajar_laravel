@extends('layouts.app')

@section('title', 'Edit Produk - TokoKita')

@section('content')
    <h2 class="mb-4">Edit Produk: {{ $barang->nama }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TODO 6a: action harus ke barang.update + method spoofing --}}
    <form method="POST" action="{{ route('barang.update', $barang->id) }}">
        @csrf
        @method('PUT') {{-- HTML form tidak punya PUT --}}

        {{-- TODO 6b: input nama memuat nilai lama (old bila validasi gagal) --}}
        <div class="mb-3">
            <label class="form-label">Nama Produk</label>
            <input type="text" name="nama" class="form-control"
                value="{{ old('nama', $barang->nama) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">SKU</label>
            <input type="text" name="sku" class="form-control"
                value="{{ old('sku', $barang->sku) }}" required>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" class="form-control"
                    value="{{ old('harga', $barang->harga) }}" min="0" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Stok</label>
                <input type="number" name="stok" class="form-control"
                    value="{{ old('stok', $barang->stok) }}" min="0" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Kategori</label>
                {{-- TODO 6c: dropdown kategori harus terpilih sesuai data lama --}}
                <select name="kategori_id" class="form-select">
                    <option value="">- pilih kategori -</option>
                    @forelse($kategoris as $k)
                        <option value="{{ $k->id }}"
                            {{ old('kategori_id', $barang->kategori_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @empty
                        <option value="">Belum ada kategori</option>
                    @endforelse
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection
