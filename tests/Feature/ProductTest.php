<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * Test that the product page loads successfully and receives product variables.
     */
    public function test_product_page_can_be_rendered(): void
    {
        $response = $this->get('/product');

        $response->assertStatus(200);
        $response->assertViewHas(['var_product', 'products', 'product']);
        $response->assertSee('Koleksi Eksklusif Cyberpunk 2026 & Next-Gen Tech');
        $response->assertSee('ini adalah halaman produck kata bapaknya');
    }
}
