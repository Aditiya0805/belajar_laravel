@props(['nama', 'harga', 'stok' => 0, 'kategori' => '-'])

<div class="col-md-4 mb-3">
    <div class="card h-100 shadow-sm">
        <div class="card-body">
            {{-- TODO 2a: tampilkan nama produk (props 'nama') --}}
            <h5 class="card-title">{{ $nama }}</h5>

            {{-- TODO 13: tampilkan nama kategori (Sesi 3) --}}
            @if($kategori && $kategori !== '-')
                <small class="text-muted d-block mb-2">{{ $kategori }}</small>
            @endif

            {{-- TODO 2b: tampilkan harga dalam format Rupiah --}}
            <p class="card-text text-success">Rp {{ number_format($harga, 0, ',', '.') }}</p>

            {{-- TODO 2c: tampilkan stok; bila 0 tampilkan teks 'HABIS' --}}
            <span class="badge {{ $stok > 0 ? 'bg-success' : 'bg-danger' }}">
                {{ $stok > 0 ? 'Stok: ' . $stok : 'HABIS' }}
            </span>
        </div>
        @if($slot->isNotEmpty())
            <div class="card-footer bg-transparent border-top-0 d-flex gap-2 pb-3">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
