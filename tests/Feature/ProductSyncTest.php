<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductSyncTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_woocommerce_product_sync()
    {
        $this->artisan('sync:wooproducts')
            ->expectsOutput('WooCommerce Product Synchronize...')
    }
}
