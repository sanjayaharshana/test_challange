<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ApiResponse;
use Illuminate\Support\Facades\Http;
use Spatie\FlareClient\Api;
use App\Jobs\SyncWooComProduct;
class ProductController extends Controller
{
    public function index()
    {
        $product = Product::all();
        return response()->json($product);
    }

    public static function calulcateLoopTime($currentPageNum,$totalPageNum)
    {
        $totaDetails = $totalPageNum - $currentPageNum;
        return $totaDetails;
    }












    public function test()
    {

        $tapline = SyncWooComProduct::dispatch(new SyncWooComProduct(1));
    }
}
