<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use Mockery\Exception;

class ApiResponse extends Model
{
    use HasFactory;

    /**
     * Function Name: startResponse
     * Author: Sanjaya Senevirathne
     * Description: Store API Response Details
     *
     * @param $url
     * @param $body
     * @param $resSecounds
     * @param $status
     * @param $startTime
     * @param $endTime
     * @return mixed
     *
     */

    public static function startResponse($url,$body,$resSecounds,$status,$startTime,$endTime,$page_number,$product_count,$total_page_count)
    {
        $responseDetails = new ApiResponse;
        $responseDetails->endpoint_url = $url;
        $responseDetails->response_data = $body;
        $responseDetails->response_time = $resSecounds ;
        $responseDetails->status_code = $status;
        $responseDetails->start_time = $startTime;
        $responseDetails->end_time = $endTime;
        $responseDetails->product_count = $product_count;
        $responseDetails->total_page_count = $total_page_count;
        if($page_number == null)
        {
            $responseDetails->page_number = 1;
        }else{
            $responseDetails->page_number = $page_number;
        }
        $responseDetails->save();
        Log::info('Response: Saved: ResponseID: '.$responseDetails->id);
        return $responseDetails->id;
    }

    /**
     *
     * Function Name: getLastEndpointDetails
     * Author: Sanjaya Senevirathne
     * Description: get last api request details by url
     *
     * @param $url
     * @return mixed
     */

    public static function getLastEndpointDetails($url)
    {
        $apiResponseDetails = ApiResponse::where('endpoint_url',$url)
            ->latest()
            ->first();
        return $apiResponseDetails;
    }

    /**
     * Function Name: getTotalProducts (from Response)
     * Author: Sanjaya Senevirathne
     * Description: get total product from response API
     * @param $response
     * @return mixed
     */

    public static function getTotalProducts($response)
    {
        try{
            $totalProductsArray = $response->headers()['x-wp-total'];
            Log::info('Total Products: '.$totalProductsArray[0]);
            return $totalProductsArray[0];
        }catch (\Exception $exception)
        {
            Log::critical($exception);
            return null;
        }

    }

    /**
     * Function Name: getTotalPages
     * Aauthor: Sanjaya Senevirathne
     * Description: get total pages from response API
     * @param $response
     * @return null
     */
    public static function getTotalPages($response){
        try{
            $totalPageArray = $response->headers()['x-wp-totalpages'];
            Log::info('Total Pages: '.$totalPageArray[0]);
            return $totalPageArray[0];
        }catch (\Exception $exception)
        {
            Log::critical($exception);
            return null;
        }

    }

    /**
     * Function Name: saveProductFromResonse
     * Author : Sanjaya Senevirathne
     * Description : get Save API products to product_table
     * @param $response
     */

    public static function saveProductFromResponse($response)
    {
        $outputProducts = json_decode($response->body());
        if($response->status() == 200){
            foreach ($outputProducts as $wooitem) {
                if (Product::where('product_id', $wooitem->id)->first() == null) {
                    // WooCommerce Product added ProductTable
                    $prodDetails = new Product;
                    $prodDetails->product_id = $wooitem->id;
                    $prodDetails->name = $wooitem->name;
                    $prodDetails->price = number_format($wooitem->price, 2);
                    $prodDetails->description = strip_tags($wooitem->description);
                    $prodDetails->save();
                    Log::info('WoooCommerce ProductID: ' . $wooitem->id . '(' . $wooitem->name . ') added');
                }
            }
        }else{
            Log::critical('Invalid Response: ' . $response->status());
        }
    }

    public static function callAPIGetProducts($page = null)
    {
        $lastResopnseData = self::getLastEndpointDetails(config('app.woocom_endpoint'));
//        if($lastResopnseData->){
//
//        }else{
//
//        }

        $startTime = microtime(true);
        if($page){
            $response = $response = Http::withBasicAuth(config('app.woocom_cusum_key'), config('app.woocom_cusum_secret'))
                ->get(config('app.woocom_endpoint').'?per_page='.config('app.product_sync_limit').'&page='.$page);
            $responseTime = (microtime(true) - $startTime)/60 ;
        }else{
            $response = Http::withBasicAuth(config('app.woocom_cusum_key'), config('app.woocom_cusum_secret'))
                ->get(config('app.woocom_endpoint').'?per_page='.config('app.product_sync_limit'));
            $responseTime = (microtime(true) - $startTime)/60 ;
        }
        //Store API Response Detail APIReponse Table;
        $responseDetails = new ApiResponse;
        $responseDetails::startResponse(
            config('app.woocom_endpoint'),
            $response->body(),
            $responseTime,$response->status(),
            $startTime,microtime(true),
            $page,
            self::getTotalProducts($response),
            self::getTotalPages($response));


        $outputData = [
            'response_time' => $responseTime,
            'response' => $response,
            'page' => $page,
            'page_count' => self::getTotalPages($response),
            'total_products' => self::getTotalProducts($response)
        ];
        return $outputData;
    }
}
