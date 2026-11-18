<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Product Showcase - {{ $var_product ?? 'Katalog Produk' }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Three.js CDN untuk Animasi 3D Real-time -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"Space Grotesk"', 'monospace'],
                    },
                    colors: {
                        cyber: {
                            dark: '#070913',
                            card: '#0f1322',
                            neonCyan: '#06b6d4',
                            neonPurple: '#a855f7',
                            neonEmerald: '#10b981',
                            neonRose: '#f43f5e',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #070913;
            color: #f1f5f9;
            overflow-x: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(6, 182, 212, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 85% 25%, rgba(168, 85, 247, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 50% 80%, rgba(16, 185, 129, 0.08) 0%, transparent 50%),
                linear-gradient(to bottom, #070913, #0b0e1b, #070913);
            background-attachment: fixed;
        }

        /* 3D Perspective Grid Background */
        .cyber-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: 60px 60px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            pointer-events: none;
            z-index: 0;
        }

        /* 3D Card Container */
        .card-3d-wrap {
            perspective: 1200px;
            transform-style: preserve-3d;
        }

        .card-3d {
            transition: transform 0.2s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.3s ease, border-color 0.3s ease;
            transform-style: preserve-3d;
            will-change: transform;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .card-3d:hover {
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.7),
                0 0 35px -5px var(--glow-color, rgba(6, 182, 212, 0.35));
        }

        /* Multi-layer 3D Depth Elements */
        .depth-bg {
            transform: translateZ(0px);
        }

        .depth-canvas {
            transform: translateZ(30px);
            transform-style: preserve-3d;
        }

        .depth-content {
            transform: translateZ(45px);
        }

        .depth-badge {
            transform: translateZ(60px);
        }

        .depth-btn {
            transform: translateZ(50px);
        }

        /* Glare effect on card */
        .glare {
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(255, 255, 255, 0.15) 0%, transparent 65%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 10;
        }

        .card-3d:hover .glare {
            opacity: 1;
        }

        /* CSS 3D Floating Cube */
        .cube-scene {
            width: 70px;
            height: 70px;
            perspective: 600px;
        }

        .cube {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            animation: rotateCube 12s infinite linear;
        }

        .cube-face {
            position: absolute;
            width: 70px;
            height: 70px;
            border: 1.5px solid rgba(6, 182, 212, 0.6);
            background: rgba(6, 182, 212, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            backdrop-filter: blur(4px);
            box-shadow: inset 0 0 15px rgba(6, 182, 212, 0.2);
        }

        .cube-face--front  { transform: rotateY(  0deg) translateZ(35px); }
        .cube-face--back   { transform: rotateY(180deg) translateZ(35px); }
        .cube-face--right  { transform: rotateY( 90deg) translateZ(35px); }
        .cube-face--left   { transform: rotateY(-90deg) translateZ(35px); }
        .cube-face--top    { transform: rotateX( 90deg) translateZ(35px); }
        .cube-face--bottom { transform: rotateX(-90deg) translateZ(35px); }

        @keyframes rotateCube {
            0% { transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg); }
            100% { transform: rotateX(360deg) rotateY(720deg) rotateZ(360deg); }
        }

        /* Floating Animation */
        @keyframes floatHero {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(1.5deg); }
        }

        .animate-float {
            animation: floatHero 6s ease-in-out infinite;
        }

        /* Glassmorphism Classes */
        .glass-panel {
            background: rgba(15, 19, 34, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-panel-glow {
            background: rgba(15, 19, 34, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(6, 182, 212, 0.25);
            box-shadow: 0 10px 40px -10px rgba(6, 182, 212, 0.15);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #070913;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #06b6d4;
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 antialiased selection:bg-cyan-500 selection:text-black">

    <div class="cyber-grid"></div>

    <!-- Navigation Bar -->
    <header class="relative z-50 border-b border-white/10 glass-panel sticky top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Mini 3D Cube Icon -->
                <div class="cube-scene hidden sm:block scale-75 origin-center">
                    <div class="cube">
                        <div class="cube-face cube-face--front">💎</div>
                        <div class="cube-face cube-face--back">⚡</div>
                        <div class="cube-face cube-face--right">🚀</div>
                        <div class="cube-face cube-face--left">🔮</div>
                        <div class="cube-face cube-face--top">✨</div>
                        <div class="cube-face cube-face--bottom">🛡️</div>
                    </div>
                </div>

                <div>
                    <a href="{{ url('/') }}" class="text-xl font-extrabold tracking-tight flex items-center gap-2 group">
                        <span class="bg-gradient-to-r from-cyan-400 via-purple-400 to-pink-500 bg-clip-text text-transparent group-hover:opacity-80 transition">
                            LARAVEL 3D
                        </span>
                        <span class="text-xs font-mono uppercase px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">v1.0</span>
                    </a>
                    <p class="text-xs text-slate-400 font-mono tracking-wide">Interactive 3D Experience</p>
                </div>
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Sound SFX Toggle -->
                <button id="sfx-toggle" onclick="toggleAudio()" class="px-3 py-1.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 text-xs font-mono text-slate-300 flex items-center gap-2 transition" title="Toggle Sci-Fi SFX">
                    <i data-lucide="volume-2" class="w-4 h-4 text-cyan-400"></i>
                    <span id="sfx-label" class="hidden sm:inline">Audio: AKTIF</span>
                </button>

                <!-- Back to Welcome Link -->
                <a href="{{ url('/') }}" class="px-4 py-2 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 hover:border-cyan-500/40 text-sm font-medium text-slate-300 hover:text-white flex items-center gap-2 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Beranda</span>
                </a>

                <!-- Cart Button -->
                <button onclick="showNotification('Keranjang belanja saat ini memiliki 2 produk 3D!')" class="relative p-2.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 transition">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                    <span id="cart-count" class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-cyan-500 text-black font-bold text-xs flex items-center justify-center">2</span>
                </button>
            </div>
        </div>
    </header>

    <main class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- Top Breadcrumb / Original Note Preservation -->
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full glass-panel border border-cyan-500/30 shadow-lg shadow-cyan-500/5">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-ping"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 -ml-5"></span>
                <span class="text-xs sm:text-sm font-mono text-cyan-300 font-semibold uppercase tracking-wider">
                    // status: <span class="text-slate-300">ini adalah halaman produck kata bapaknya</span>
                </span>
            </div>

            <!-- 3D View Controls -->
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 font-mono hidden md:inline">Mode Interaksi 3D:</span>
                <button onclick="toggleWireframe()" class="px-3 py-1.5 text-xs font-mono rounded-lg border border-white/10 bg-white/5 hover:bg-cyan-500/20 hover:border-cyan-500/40 text-cyan-400 transition flex items-center gap-1.5">
                    <i data-lucide="box" class="w-3.5 h-3.5"></i>
                    <span>Toggle Wireframe</span>
                </button>
                <button onclick="resetCamera()" class="px-3 py-1.5 text-xs font-mono rounded-lg border border-white/10 bg-white/5 hover:bg-purple-500/20 hover:border-purple-500/40 text-purple-400 transition flex items-center gap-1.5">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Reset Sudut 3D</span>
                </button>
            </div>
        </div>

        <!-- HERO SECTION WITH INTERACTIVE THREE.JS 3D SHOWCASE -->
        <section class="mb-16 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7 space-y-6">
                <!-- Variable Product Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gradient-to-r from-purple-500/20 to-pink-500/20 border border-purple-500/30 text-purple-300 text-xs font-semibold uppercase tracking-widest">
                    <i data-lucide="sparkles" class="w-4 h-4 text-pink-400"></i>
                    <span>Variable $var_product Aktif</span>
                </div>

                <!-- Main Heading rendering $var_product -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                    <span class="block text-slate-100">Jelajahi Dunia</span>
                    <span class="bg-gradient-to-r from-cyan-400 via-teal-300 to-indigo-400 bg-clip-text text-transparent">
                        {{ $var_product }}
                    </span>
                </h1>

                <p class="text-slate-300 text-base sm:text-lg leading-relaxed max-w-2xl font-normal">
                    Pengalaman belanja produk masa depan dengan rendering 3D interaktif real-time. Gerakkan kursor Anda pada setiap kartu untuk merasakan efek kedalaman <span class="text-cyan-400 font-semibold">3D Tilt & Holographic Lighting</span>!
                </p>

                <!-- Feature Quick Stats -->
                <div class="grid grid-cols-3 gap-4 pt-2 max-w-lg">
                    <div class="p-3.5 rounded-2xl glass-panel border border-white/5 text-center">
                        <div class="text-2xl font-extrabold text-cyan-400 font-mono">4+</div>
                        <div class="text-xs text-slate-400 mt-0.5">Model 3D Real-time</div>
                    </div>
                    <div class="p-3.5 rounded-2xl glass-panel border border-white/5 text-center">
                        <div class="text-2xl font-extrabold text-purple-400 font-mono">60 FPS</div>
                        <div class="text-xs text-slate-400 mt-0.5">WebGL Acceleration</div>
                    </div>
                    <div class="p-3.5 rounded-2xl glass-panel border border-white/5 text-center">
                        <div class="text-2xl font-extrabold text-emerald-400 font-mono">100%</div>
                        <div class="text-xs text-slate-400 mt-0.5">Interaktif & Responsif</div>
                    </div>
                </div>

                <!-- Action CTA -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="#product-catalog" class="px-7 py-3.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-sm tracking-wide shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40 hover:-translate-y-0.5 transition flex items-center gap-2">
                        <span>Lihat Semua Produk</span>
                        <i data-lucide="arrow-down" class="w-4 h-4"></i>
                    </a>
                    <button onclick="inspectProduct(0)" class="px-6 py-3.5 rounded-xl glass-panel border border-cyan-500/30 hover:border-cyan-400 text-cyan-300 font-semibold text-sm hover:bg-cyan-500/10 transition flex items-center gap-2">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                        <span>Inspeksi 3D Model Utama</span>
                    </button>
                </div>
            </div>

            <!-- Hero 3D Interactive Canvas -->
            <div class="lg:col-span-5 relative">
                <div class="relative w-full aspect-square max-w-[460px] mx-auto rounded-3xl glass-panel-glow overflow-hidden p-1">
                    <!-- ThreeJS 3D WebGL Canvas -->
                    <div id="hero-canvas-container" class="w-full h-full rounded-[22px] overflow-hidden cursor-grab active:cursor-grabbing relative">
                        <div class="absolute top-4 left-4 z-20 pointer-events-none">
                            <span class="px-2.5 py-1 rounded-md bg-black/60 backdrop-blur-md text-xs font-mono text-cyan-400 border border-cyan-500/30 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                                WebGL 3D Core: <span id="hero-shape-name">Icosahedron</span>
                            </span>
                        </div>
                        <div class="absolute bottom-4 right-4 z-20 pointer-events-none text-right">
                            <span class="text-[11px] font-mono text-slate-400 bg-black/60 backdrop-blur-md px-2 py-1 rounded border border-white/10">
                                🖱️ Klik & Tarik untuk Rotasi 360°
                            </span>
                        </div>
                    </div>

                    <!-- Ambient Glow Behind Canvas -->
                    <div class="absolute -inset-4 bg-gradient-to-r from-cyan-500/20 via-purple-500/20 to-pink-500/20 blur-2xl -z-10 opacity-70"></div>
                </div>
            </div>
        </section>

        <!-- PRODUCT CATALOG WITH 3D TILT CARDS -->
        <section id="product-catalog" class="pt-6 pb-20">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 pb-4 border-b border-white/10 gap-4">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                        <span>Katalog Produk Unggulan</span>
                        <span class="text-sm font-normal font-mono px-3 py-1 rounded-full bg-white/5 border border-white/10 text-cyan-400">
                            {{ count($products) }} Item Tersedia
                        </span>
                    </h2>
                    <p class="text-slate-400 text-sm mt-1">Kartu interaktif dengan efek 3D Parallax & Depth Layering.</p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono text-slate-400">Filter Efek:</span>
                    <button onclick="changeCardsColor('#06b6d4')" class="w-6 h-6 rounded-full bg-cyan-500 ring-2 ring-transparent hover:ring-white transition" title="Cyan Glow"></button>
                    <button onclick="changeCardsColor('#a855f7')" class="w-6 h-6 rounded-full bg-purple-500 ring-2 ring-transparent hover:ring-white transition" title="Purple Glow"></button>
                    <button onclick="changeCardsColor('#10b981')" class="w-6 h-6 rounded-full bg-emerald-500 ring-2 ring-transparent hover:ring-white transition" title="Emerald Glow"></button>
                    <button onclick="changeCardsColor('#f43f5e')" class="w-6 h-6 rounded-full bg-rose-500 ring-2 ring-transparent hover:ring-white transition" title="Rose Glow"></button>
                </div>
            </div>

            <!-- 3D Card Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-7">
                @foreach($products as $index => $item)
                <div class="card-3d-wrap" onmousemove="handleTilt(event, this)" onmouseleave="resetTilt(this)">
                    <div class="card-3d relative h-full rounded-2xl p-6 glass-panel border border-white/10 overflow-hidden flex flex-col justify-between group"
                         style="--glow-color: {{ $item['color'] }};"
                         data-color="{{ $item['color'] }}"
                         data-shape="{{ $item['shape'] }}"
                         data-name="{{ $item['name'] }}">
                        
                        <!-- Glare Highlight Layer -->
                        <div class="glare"></div>

                        <!-- Card Top: Badge & Category -->
                        <div>
                            <div class="flex items-center justify-between mb-4 depth-badge">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/10 text-slate-200 border border-white/15">
                                    {{ $item['badge'] }}
                                </span>
                                <span class="text-xs font-mono text-slate-400">
                                    {{ $item['category'] }}
                                </span>
                            </div>

                            <!-- Interactive Mini 3D Preview Canvas -->
                            <div class="relative w-full h-44 rounded-xl bg-slate-950/60 border border-white/5 overflow-hidden mb-5 depth-canvas flex items-center justify-center group-hover:border-cyan-500/30 transition">
                                <div id="mini-3d-{{ $index }}" class="w-full h-full"></div>
                                
                                <!-- Floating 3D Badge Indicator -->
                                <button onclick="inspectProduct({{ $index }})" class="absolute bottom-2 right-2 px-2.5 py-1 rounded-lg bg-black/70 backdrop-blur-md text-[11px] font-mono text-cyan-300 border border-cyan-500/30 hover:bg-cyan-500 hover:text-black transition flex items-center gap-1">
                                    <i data-lucide="maximize-2" class="w-3 h-3"></i>
                                    <span>3D Zoom</span>
                                </button>
                            </div>

                            <!-- Product Info -->
                            <div class="depth-content space-y-2">
                                <div class="flex items-center gap-1.5 text-xs text-amber-400 font-mono">
                                    <span>★ {{ $item['rating'] }}</span>
                                    <span class="text-slate-500">({{ $item['reviews'] }} review)</span>
                                </div>

                                <h3 class="text-lg font-bold text-white group-hover:text-cyan-300 transition line-clamp-1">
                                    {{ $item['name'] }}
                                </h3>

                                <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                                    {{ $item['description'] }}
                                </p>

                                <!-- Specs Tags -->
                                <div class="flex flex-wrap gap-1.5 pt-2">
                                    @foreach($item['specs'] as $spec)
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-white/5 text-slate-300 border border-white/5">
                                        {{ $spec }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Card Bottom: Price and Buy Actions -->
                        <div class="pt-6 mt-4 border-t border-white/10 depth-btn">
                            <div class="flex items-baseline justify-between mb-3">
                                <div>
                                    <span class="text-xs text-slate-500 line-through block">{{ $item['original_price'] }}</span>
                                    <span class="text-lg font-extrabold text-white font-mono">{{ $item['price'] }}</span>
                                </div>
                                <span class="text-[10px] font-mono text-emerald-400 px-1.5 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20">Hemat 15%</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="addToCart('{{ $item['name'] }}')" class="py-2.5 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-medium text-xs flex items-center justify-center gap-1.5 transition">
                                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                    <span>Keranjang</span>
                                </button>
                                <button onclick="buyNow('{{ $item['name'] }}')" class="py-2.5 px-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-bold text-xs flex items-center justify-center gap-1.5 shadow-md shadow-cyan-500/20 transition">
                                    <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                                    <span>Beli Cepat</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- SINGLE PRODUCT HIGHLIGHT DETAIL ($product) -->
        <section class="mb-20">
            <div class="p-8 sm:p-10 rounded-3xl glass-panel border border-cyan-500/30 relative overflow-hidden">
                <!-- Background Ambient Lights -->
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                    <div class="lg:col-span-7 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-mono">
                            <i data-lucide="award" class="w-3.5 h-3.5"></i>
                            <span>PRODUK PILIHAN UTAMA ($product)</span>
                        </div>

                        <h3 class="text-3xl font-extrabold text-white">
                            {{ $product['name'] }}
                        </h3>

                        <p class="text-slate-300 text-sm leading-relaxed max-w-xl">
                            {{ $product['description'] }} Dilengkapi arsitektur komputasi optik kuantum generasi terbaru yang menghadirkan performa tanpa hambatan dengan efisiensi energi tertinggi.
                        </p>

                        <div class="flex items-center gap-6 pt-2 font-mono">
                            <div>
                                <span class="text-xs text-slate-400 block">Harga Spesial:</span>
                                <span class="text-2xl font-bold text-cyan-400">{{ $product['price'] }}</span>
                            </div>
                            <div class="h-8 w-px bg-white/10"></div>
                            <div>
                                <span class="text-xs text-slate-400 block">Garansi Resmi:</span>
                                <span class="text-sm font-semibold text-white">3 Tahun Ganti Baru</span>
                            </div>
                        </div>

                        <div class="pt-4 flex flex-wrap gap-3">
                            <button onclick="inspectProduct(0)" class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-400 hover:to-teal-400 text-slate-950 font-bold text-xs uppercase tracking-wider flex items-center gap-2 shadow-lg shadow-cyan-500/25 transition">
                                <i data-lucide="scan-eye" class="w-4 h-4"></i>
                                <span>Lihat Model 3D Interaktif</span>
                            </button>
                            <button onclick="buyNow('{{ $product['name'] }}')" class="px-6 py-3 rounded-xl border border-white/20 hover:border-white text-white font-semibold text-xs uppercase tracking-wider transition">
                                <span>Pesan Sekarang</span>
                            </button>
                        </div>
                    </div>

                    <div class="lg:col-span-5 flex justify-center">
                        <!-- Holographic 3D Floating Display -->
                        <div class="relative w-64 h-64 flex items-center justify-center animate-float">
                            <!-- Rotating Glow Rings -->
                            <div class="absolute inset-0 rounded-full border-2 border-dashed border-cyan-500/40 animate-spin" style="animation-duration: 20s;"></div>
                            <div class="absolute inset-4 rounded-full border border-purple-500/30 animate-spin" style="animation-duration: 15s; animation-direction: reverse;"></div>
                            <div class="absolute inset-10 rounded-full bg-gradient-to-tr from-cyan-500/10 via-purple-500/10 to-pink-500/10 backdrop-blur-xl border border-white/10 flex items-center justify-center">
                                <div class="text-center p-4">
                                    <div class="text-4xl mb-2">💎</div>
                                    <div class="text-xs font-mono text-cyan-300 font-bold">QUANTUM CORE</div>
                                    <div class="text-[10px] text-slate-400">Synchronized 3D</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- 3D INSPECTION MODAL (Full 3D Viewer) -->
    <div id="inspector-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xl hidden items-center justify-center p-4">
        <div class="relative w-full max-w-4xl h-[85vh] rounded-3xl glass-panel-glow border border-cyan-500/40 overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between bg-black/40">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-cyan-400 animate-pulse"></div>
                    <div>
                        <h4 id="modal-product-name" class="text-lg font-bold text-white">Quantum HoloCore V2</h4>
                        <p class="text-xs font-mono text-cyan-400">Interactive 3D Real-time Inspector</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="toggleModalWireframe()" class="px-3 py-1.5 text-xs font-mono rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 text-slate-300 transition">
                        Wireframe
                    </button>
                    <button onclick="closeInspector()" class="p-2 rounded-lg bg-white/10 hover:bg-rose-500/20 hover:text-rose-400 text-slate-300 transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <!-- 3D Full Canvas -->
            <div id="modal-canvas-container" class="flex-1 w-full relative cursor-grab active:cursor-grabbing bg-radial from-slate-900 to-black">
                <div class="absolute bottom-6 left-6 z-10 pointer-events-none space-y-1">
                    <div class="text-xs font-mono text-slate-300 bg-black/60 backdrop-blur px-3 py-1.5 rounded-lg border border-white/10">
                        Controls: Klik & Geser untuk memutar | Scroll untuk Zoom
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-white/10 bg-black/40 flex items-center justify-between">
                <div>
                    <span id="modal-product-price" class="text-xl font-bold font-mono text-cyan-400">Rp 12.850.000</span>
                    <span class="text-xs text-slate-400 block">Termasuk PPN & Garansi 3 Tahun</span>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="closeInspector()" class="px-5 py-2.5 rounded-xl border border-white/10 hover:bg-white/5 text-xs font-semibold text-slate-300 transition">
                        Tutup
                    </button>
                    <button id="modal-buy-btn" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-black font-bold text-xs transition">
                        Beli Produk Ini
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Component -->
    <div id="toast" class="fixed bottom-6 right-6 z-50 transform translate-y-24 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="px-5 py-3.5 rounded-2xl glass-panel-glow border border-cyan-500/50 flex items-center gap-3 text-sm text-white shadow-2xl">
            <div class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <p id="toast-title" class="font-bold text-xs uppercase tracking-wider text-cyan-400">Informasi</p>
                <p id="toast-msg" class="text-xs text-slate-200">Aksi berhasil dijalankan.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="border-t border-white/10 py-10 relative z-10 glass-panel">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 font-mono space-y-2">
            <p>Laravel Boost & 3D Interactive Showcase // Powered by Three.js & Tailwind CSS</p>
            <p class="text-slate-600">Dibuat khusus untuk eksplorasi Laravel modern dengan animasi 3D mutakhir.</p>
        </div>
    </footer>

    <!-- Three.js 3D Engine & Scripts -->
    <script>
        // Inisialisasi Lucide Icons
        lucide.createIcons();

        // Data Produk dari PHP Laravel Controller
        const productsData = @json($products);

        /* ==========================================================
           1. Web Audio API (Futuristic SFX)
           ========================================================== */
        let audioCtx = null;
        let sfxEnabled = true;

        function getAudioContext() {
            if (!audioCtx) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (AudioContext) {
                    audioCtx = new AudioContext();
                }
            }
            if (audioCtx && audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            return audioCtx;
        }

        function playSciFiBeep(freq = 600, type = 'sine', duration = 0.08) {
            if (!sfxEnabled) return;
            try {
                const ctx = getAudioContext();
                if (!ctx) return;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = type;
                osc.frequency.setValueAtTime(freq, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(freq * 1.5, ctx.currentTime + duration);
                gain.gain.setValueAtTime(0.04, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + duration);
            } catch (e) {
                // Audio might be blocked by browser policy until interaction
            }
        }

        function toggleAudio() {
            sfxEnabled = !sfxEnabled;
            const label = document.getElementById('sfx-label');
            label.textContent = sfxEnabled ? 'Audio: AKTIF' : 'Audio: MATI';
            showNotification(sfxEnabled ? 'Efek audio sci-fi diaktifkan' : 'Efek audio dimatikan');
        }

        /* ==========================================================
           2. 3D Tilt Card Interactive Physics
           ========================================================== */
        function handleTilt(e, wrap) {
            const card = wrap.querySelector('.card-3d');
            const rect = wrap.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = ((y - centerY) / centerY) * -14;
            const rotateY = ((x - centerX) / centerX) * 14;

            card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;

            const percentX = (x / rect.width) * 100;
            const percentY = (y / rect.height) * 100;
            card.style.setProperty('--mouse-x', `${percentX}%`);
            card.style.setProperty('--mouse-y', `${percentY}%`);
        }

        function resetTilt(wrap) {
            const card = wrap.querySelector('.card-3d');
            card.style.transform = 'rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
        }

        function changeCardsColor(hexColor) {
            document.querySelectorAll('.card-3d').forEach(c => {
                c.style.setProperty('--glow-color', hexColor);
            });
            playSciFiBeep(880, 'sine', 0.1);
            showNotification(`Tema warna 3D diubah ke ${hexColor}`);
        }

        /* ==========================================================
           3. Three.js Hero 3D Interactive Scene
           ========================================================== */
        let heroScene, heroCamera, heroRenderer, heroMesh, heroWireframeMesh, heroRings = [], heroParticles;
        let heroIsWireframe = false;
        let heroTargetRotationX = 0, heroTargetRotationY = 0;
        let isHeroDragging = false;
        let heroPreviousMousePosition = { x: 0, y: 0 };

        function initHero3D() {
            const container = document.getElementById('hero-canvas-container');
            if (!container) return;

            const width = container.clientWidth;
            const height = container.clientHeight;

            heroScene = new THREE.Scene();
            heroCamera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
            heroCamera.position.z = 5.2;

            heroRenderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            heroRenderer.setSize(width, height);
            heroRenderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            container.appendChild(heroRenderer.domElement);

            // Lights
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
            heroScene.add(ambientLight);

            const pointLight1 = new THREE.PointLight(0x06b6d4, 3, 50);
            pointLight1.position.set(4, 5, 4);
            heroScene.add(pointLight1);

            const pointLight2 = new THREE.PointLight(0xa855f7, 2.5, 50);
            pointLight2.position.set(-4, -5, -2);
            heroScene.add(pointLight2);

            // Core 3D Geometry: Icosahedron
            const geometry = new THREE.IcosahedronGeometry(1.4, 0);
            const material = new THREE.MeshPhysicalMaterial({
                color: 0x06b6d4,
                emissive: 0x034b57,
                roughness: 0.15,
                metalness: 0.9,
                reflectivity: 1,
                clearcoat: 1,
                clearcoatRoughness: 0.1,
                wireframe: false
            });

            heroMesh = new THREE.Mesh(geometry, material);
            heroScene.add(heroMesh);

            // Outer Wireframe Hologram
            const wireframeGeometry = new THREE.IcosahedronGeometry(1.65, 1);
            const wireframeMaterial = new THREE.MeshBasicMaterial({
                color: 0x38bdf8,
                wireframe: true,
                transparent: true,
                opacity: 0.4
            });
            heroWireframeMesh = new THREE.Mesh(wireframeGeometry, wireframeMaterial);
            heroScene.add(heroWireframeMesh);

            // Glowing Orbit Rings
            for (let i = 0; i < 2; i++) {
                const ringGeo = new THREE.TorusGeometry(2.1 + i * 0.3, 0.02, 16, 100);
                const ringMat = new THREE.MeshBasicMaterial({
                    color: i === 0 ? 0x06b6d4 : 0xa855f7,
                    transparent: true,
                    opacity: 0.6
                });
                const ring = new THREE.Mesh(ringGeo, ringMat);
                ring.rotation.x = Math.PI / (2.5 + i);
                ring.rotation.y = Math.PI / (4 + i);
                heroScene.add(ring);
                heroRings.push(ring);
            }

            // Particle Galaxy
            const particleCount = 200;
            const particleGeo = new THREE.BufferGeometry();
            const positions = new Float32Array(particleCount * 3);

            for (let i = 0; i < particleCount * 3; i += 3) {
                positions[i] = (Math.random() - 0.5) * 10;
                positions[i + 1] = (Math.random() - 0.5) * 10;
                positions[i + 2] = (Math.random() - 0.5) * 10;
            }

            particleGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
            const particleMat = new THREE.PointsMaterial({
                size: 0.05,
                color: 0x38bdf8,
                transparent: true,
                opacity: 0.7
            });
            heroParticles = new THREE.Points(particleGeo, particleMat);
            heroScene.add(heroParticles);

            // Mouse & Touch Drag Controls
            container.addEventListener('mousedown', (e) => {
                isHeroDragging = true;
                heroPreviousMousePosition = { x: e.clientX, y: e.clientY };
                playSciFiBeep(520, 'sine', 0.05);
            });

            window.addEventListener('mousemove', (e) => {
                if (isHeroDragging) {
                    const deltaX = e.clientX - heroPreviousMousePosition.x;
                    const deltaY = e.clientY - heroPreviousMousePosition.y;

                    heroMesh.rotation.y += deltaX * 0.01;
                    heroMesh.rotation.x += deltaY * 0.01;
                    heroWireframeMesh.rotation.y += deltaX * 0.01;
                    heroWireframeMesh.rotation.x += deltaY * 0.01;

                    heroPreviousMousePosition = { x: e.clientX, y: e.clientY };
                }
            });

            window.addEventListener('mouseup', () => { isHeroDragging = false; });

            // Interactive Parallax on Mouse Move
            window.addEventListener('mousemove', (e) => {
                const mouseX = (e.clientX / window.innerWidth) * 2 - 1;
                const mouseY = -(e.clientY / window.innerHeight) * 2 + 1;
                heroTargetRotationY = mouseX * 0.5;
                heroTargetRotationX = mouseY * 0.5;
            });

            // Resize Handler
            window.addEventListener('resize', onHeroResize);

            animateHero();
        }

        function onHeroResize() {
            const container = document.getElementById('hero-canvas-container');
            if (!container || !heroRenderer || !heroCamera) return;
            const w = container.clientWidth;
            const h = container.clientHeight;
            heroCamera.aspect = w / h;
            heroCamera.updateProjectionMatrix();
            heroRenderer.setSize(w, h);
        }

        function animateHero() {
            requestAnimationFrame(animateHero);

            if (heroMesh && heroWireframeMesh) {
                if (!isHeroDragging) {
                    heroMesh.rotation.y += 0.008;
                    heroMesh.rotation.x += 0.004;
                    heroWireframeMesh.rotation.y -= 0.006;
                    heroWireframeMesh.rotation.z += 0.003;
                }

                heroRings.forEach((ring, idx) => {
                    ring.rotation.z += 0.005 * (idx + 1);
                    ring.rotation.x += 0.002 * (idx + 1);
                });

                if (heroParticles) {
                    heroParticles.rotation.y += 0.001;
                }
            }

            if (heroRenderer && heroScene && heroCamera) {
                heroRenderer.render(heroScene, heroCamera);
            }
        }

        function toggleWireframe() {
            heroIsWireframe = !heroIsWireframe;
            if (heroMesh) {
                heroMesh.material.wireframe = heroIsWireframe;
            }
            playSciFiBeep(700, 'square', 0.08);
            showNotification(heroIsWireframe ? 'Wireframe Mode: AKTIF' : 'Shaded Solid Mode: AKTIF');
        }

        function resetCamera() {
            if (heroMesh) {
                heroMesh.rotation.set(0, 0, 0);
                heroWireframeMesh.rotation.set(0, 0, 0);
            }
            playSciFiBeep(600, 'sine', 0.08);
            showNotification('Sudut kamera 3D direset');
        }

        /* ==========================================================
           4. Mini 3D Canvases on Each Product Card
           ========================================================== */
        const mini3DInstances = [];

        function initMini3D() {
            productsData.forEach((prod, index) => {
                const el = document.getElementById(`mini-3d-${index}`);
                if (!el) return;

                const width = el.clientWidth || 240;
                const height = el.clientHeight || 176;

                const scene = new THREE.Scene();
                const camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 100);
                camera.position.z = 4.2;

                const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
                renderer.setSize(width, height);
                renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
                el.appendChild(renderer.domElement);

                // Lighting
                const light1 = new THREE.DirectionalLight(0xffffff, 1.5);
                light1.position.set(3, 4, 3);
                scene.add(light1);

                const colorInt = parseInt(prod.color.replace('#', '0x'));
                const light2 = new THREE.PointLight(colorInt, 2, 20);
                light2.position.set(-3, -3, 2);
                scene.add(light2);

                // Different 3D Geometries based on product shape
                let geo;
                switch (prod.shape) {
                    case 'icosahedron':
                        geo = new THREE.IcosahedronGeometry(1.2, 0);
                        break;
                    case 'torus':
                        geo = new THREE.TorusGeometry(1.0, 0.38, 16, 60);
                        break;
                    case 'cylinder':
                        geo = new THREE.CylinderGeometry(0.8, 0.8, 1.6, 32);
                        break;
                    case 'torusKnot':
                        geo = new THREE.TorusKnotGeometry(0.85, 0.28, 64, 16);
                        break;
                    default:
                        geo = new THREE.BoxGeometry(1.2, 1.2, 1.2);
                }

                const mat = new THREE.MeshStandardMaterial({
                    color: colorInt,
                    roughness: 0.2,
                    metalness: 0.8,
                });

                const mesh = new THREE.Mesh(geo, mat);
                scene.add(mesh);

                // Wireframe outline
                const wireMat = new THREE.MeshBasicMaterial({
                    color: 0xffffff,
                    wireframe: true,
                    transparent: true,
                    opacity: 0.2
                });
                const wireMesh = new THREE.Mesh(geo, wireMat);
                scene.add(wireMesh);

                mini3DInstances.push({ scene, camera, renderer, mesh, wireMesh, el });
            });

            animateMini3D();
        }

        function animateMini3D() {
            requestAnimationFrame(animateMini3D);
            mini3DInstances.forEach((inst, i) => {
                inst.mesh.rotation.x += 0.01;
                inst.mesh.rotation.y += 0.015;
                inst.wireMesh.rotation.x = inst.mesh.rotation.x;
                inst.wireMesh.rotation.y = inst.mesh.rotation.y;
                inst.renderer.render(inst.scene, inst.camera);
            });
        }

        /* ==========================================================
           5. Fullscreen 3D Product Inspector Modal
           ========================================================== */
        let modalScene, modalCamera, modalRenderer, modalMesh;
        let isModalDragging = false;
        let modalPrevMouse = { x: 0, y: 0 };
        let modalCurrentProduct = null;

        function initModal3D() {
            const container = document.getElementById('modal-canvas-container');
            const w = container.clientWidth || 800;
            const h = container.clientHeight || 500;

            modalScene = new THREE.Scene();
            modalCamera = new THREE.PerspectiveCamera(45, w / h, 0.1, 100);
            modalCamera.position.z = 5;

            modalRenderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            modalRenderer.setSize(w, h);
            modalRenderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            container.appendChild(modalRenderer.domElement);

            const ambLight = new THREE.AmbientLight(0xffffff, 0.9);
            modalScene.add(ambLight);

            const dirLight = new THREE.DirectionalLight(0xffffff, 2);
            dirLight.position.set(5, 5, 5);
            modalScene.add(dirLight);

            const pointLight = new THREE.PointLight(0x06b6d4, 3, 20);
            pointLight.position.set(-5, -5, -2);
            modalScene.add(pointLight);

            // Drag Controls
            container.addEventListener('mousedown', (e) => {
                isModalDragging = true;
                modalPrevMouse = { x: e.clientX, y: e.clientY };
            });

            window.addEventListener('mousemove', (e) => {
                if (isModalDragging && modalMesh) {
                    const dx = e.clientX - modalPrevMouse.x;
                    const dy = e.clientY - modalPrevMouse.y;
                    modalMesh.rotation.y += dx * 0.01;
                    modalMesh.rotation.x += dy * 0.01;
                    modalPrevMouse = { x: e.clientX, y: e.clientY };
                }
            });

            window.addEventListener('mouseup', () => { isModalDragging = false; });

            // Wheel zoom
            container.addEventListener('wheel', (e) => {
                e.preventDefault();
                modalCamera.position.z += e.deltaY * 0.003;
                modalCamera.position.z = Math.max(2, Math.min(10, modalCamera.position.z));
            }, { passive: false });

            animateModal3D();
        }

        function animateModal3D() {
            requestAnimationFrame(animateModal3D);
            if (modalMesh && !isModalDragging) {
                modalMesh.rotation.y += 0.006;
            }
            if (modalRenderer && modalScene && modalCamera) {
                modalRenderer.render(modalScene, modalCamera);
            }
        }

        function inspectProduct(index) {
            const product = productsData[index];
            modalCurrentProduct = product;

            document.getElementById('modal-product-name').textContent = product.name;
            document.getElementById('modal-product-price').textContent = product.price;
            document.getElementById('modal-buy-btn').onclick = () => buyNow(product.name);

            const modal = document.getElementById('inspector-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            if (!modalRenderer) {
                initModal3D();
            }

            // Remove previous mesh
            if (modalMesh) {
                modalScene.remove(modalMesh);
            }

            const colorInt = parseInt(product.color.replace('#', '0x'));
            let geo;
            switch (product.shape) {
                case 'icosahedron': geo = new THREE.IcosahedronGeometry(1.6, 0); break;
                case 'torus': geo = new THREE.TorusGeometry(1.3, 0.45, 24, 80); break;
                case 'cylinder': geo = new THREE.CylinderGeometry(1.1, 1.1, 2.0, 36); break;
                case 'torusKnot': geo = new THREE.TorusKnotGeometry(1.1, 0.35, 100, 24); break;
                default: geo = new THREE.BoxGeometry(1.6, 1.6, 1.6);
            }

            const mat = new THREE.MeshPhysicalMaterial({
                color: colorInt,
                emissive: 0x051520,
                roughness: 0.1,
                metalness: 0.9,
                clearcoat: 1.0,
                clearcoatRoughness: 0.1
            });

            modalMesh = new THREE.Mesh(geo, mat);
            modalScene.add(modalMesh);

            playSciFiBeep(780, 'triangle', 0.12);
        }

        function toggleModalWireframe() {
            if (modalMesh) {
                modalMesh.material.wireframe = !modalMesh.material.wireframe;
                playSciFiBeep(640, 'sine', 0.06);
            }
        }

        function closeInspector() {
            const modal = document.getElementById('inspector-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            playSciFiBeep(450, 'sine', 0.06);
        }

        /* ==========================================================
           6. User Interactions & Cart System
           ========================================================== */
        let cartTotal = 2;

        function addToCart(productName) {
            cartTotal++;
            document.getElementById('cart-count').textContent = cartTotal;
            playSciFiBeep(880, 'sine', 0.1);
            showNotification(`"${productName}" berhasil ditambahkan ke keranjang belanja!`);
        }

        function buyNow(productName) {
            playSciFiBeep(980, 'triangle', 0.15);
            showNotification(`Menuju checkout untuk "${productName}"...`);
        }

        function showNotification(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = message;

            toast.classList.remove('translate-y-24', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 3000);
        }

        // Initialize when DOM is ready
        window.addEventListener('DOMContentLoaded', () => {
            initHero3D();
            setTimeout(initMini3D, 200);
        });
    </script>
</body>
</html>
