<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adit - TokoKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container bg-white p-4 rounded shadow-sm">
        <h1 class="h3 text-primary">Selamat Datang di TokoKita</h1>
        <h2 class="h5 text-secondary">Adit Raja Coding - Praktikum Integratif Pemrograman Web 2</h2>
        <p class="mt-3">Aplikasi katalog toko online TOKOKITA (Pertemuan 13 - 15):</p>
        <div class="d-flex gap-2">
            <a href="{{ route('katalog') }}" class="btn btn-primary">Buka Katalog Produk (/katalog)</a>
            <a href="{{ url('/product') }}" class="btn btn-outline-secondary">Lihat Halaman 3D Product (/product)</a>
        </div>
    </div>
</body>
</html>
