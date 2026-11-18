@props(['type' => 'info', 'message' => ''])

<div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
    {{-- TODO 4: tampilkan isi pesan --}}
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

