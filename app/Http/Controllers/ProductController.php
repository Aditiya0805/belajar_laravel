<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products with 3D showcase.
     */
    public function index(): View
    {
        $var_product = 'Koleksi Eksklusif Cyberpunk 2026 & Next-Gen Tech';

        $products = [
            [
                'id' => 1,
                'name' => 'Quantum HoloCore V2',
                'category' => 'Holographic Tech',
                'price' => 'Rp 12.850.000',
                'original_price' => 'Rp 15.000.000',
                'rating' => 4.9,
                'reviews' => 248,
                'badge' => '🔥 Terpopuler',
                'description' => 'Proyektor holografik kuantum 3D dengan rendering partikel real-time, kontrol gestur udara, dan konektivitas neural AI.',
                'color' => '#06b6d4',
                'gradient' => 'linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%)',
                'shape' => 'icosahedron',
                'specs' => ['Resolusi 8K Holo', '60 FPS 3D Mesh', 'AI Neural Sync'],
            ],
            [
                'id' => 2,
                'name' => 'AeroSound CyberSphere',
                'category' => 'Spatial Audio',
                'price' => 'Rp 5.499.000',
                'original_price' => 'Rp 6.200.000',
                'rating' => 4.8,
                'reviews' => 195,
                'badge' => '⚡ Audio 360°',
                'description' => 'Headphone audio spasial imersif dengan ruang akustik titanium, active noise cancelling 99.8%, dan cincin RGB dinamis.',
                'color' => '#a855f7',
                'gradient' => 'linear-gradient(135deg, #a855f7 0%, #6366f1 100%)',
                'shape' => 'torus',
                'specs' => ['Spatial Audio 360°', '48 Jam Baterai', 'Titanium Driver'],
            ],
            [
                'id' => 3,
                'name' => 'Chronos Matrix Watch',
                'category' => 'Smart Wearable',
                'price' => 'Rp 7.950.000',
                'original_price' => 'Rp 9.500.000',
                'rating' => 5.0,
                'reviews' => 312,
                'badge' => '👑 Best Choice',
                'description' => 'Smartwatch elegan berlayar kristal safir 3D curved display dengan sensor biometrik cerdas dan bodi aerospace titanium.',
                'color' => '#10b981',
                'gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                'shape' => 'cylinder',
                'specs' => ['Sapphire Glass 3D', 'Health AI Radar', 'Waterproof 100m'],
            ],
            [
                'id' => 4,
                'name' => 'VisionPro Bionic Oculus',
                'category' => 'Extended Reality',
                'price' => 'Rp 21.750.000',
                'original_price' => 'Rp 24.500.000',
                'rating' => 4.95,
                'reviews' => 140,
                'badge' => '✨ Edisi Spesial',
                'description' => 'Kacamata Mixed Reality ultra-ringan dengan dual micro-OLED 120Hz dan pelacakan mata presisi sub-milimeter.',
                'color' => '#f43f5e',
                'gradient' => 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)',
                'shape' => 'torusKnot',
                'specs' => ['Dual Micro-OLED', '120Hz Refresh', 'Eye Tracking AI'],
            ],
        ];

        $product = $products[0];

        return view('product', compact('var_product', 'products', 'product'));
    }
}
