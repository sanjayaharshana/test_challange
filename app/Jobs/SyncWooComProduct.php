<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\ApiResponse;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
class SyncWooComProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            //Get Starting Time
            $startTime = microtime(true);

            // Call Woocommerce API with Http Client
            // Authentication ENV Ver or App.Config

            $response = Http::withBasicAuth(config('app.woocom_cusum_key'), config('app.woocom_cusum_secret'))
                ->get(config('app.woocom_endpoint'));
            Log::info('Requesting Endpoint: '. config('app.woocom_endpoint'));
            Log::info('Using with: '. 'Consumer Secret: '.config('app.woocom_cusum_secret'));
            Log::info('Using with: '. 'Consumer Key: '.config('app.woocom_cusum_key'));

            // Response Time Calulation Request  Now Time (Microtime) - Request Start Time / secounds;
            $responseTime = (microtime(true) - $startTime)/60 ;
            Log::info('Response Status: '. $response->status());
            Log::info('Response Time: '. $responseTime );

            // Decode jsonResponse body
            $outputProducts = json_decode($response->body());

            //Store API Response Detail APIReponse Table;
            $responseDetails = new ApiResponse;
            $responseDetails::startResponse(
                config('app.woocom_endpoint'),
                $response->body(),
                $responseTime,$response->status(),
                $startTime,microtime(true));

            // Define i variable for loop counts
            $i = 0;

            if($response->status() == 200){
                Log::info('Response Product Count: '. count($outputProducts) );
                Log::info('Product Limit: '. config('app.product_sync_limit') );
                if(count($outputProducts) !=0)
                {
                    foreach ($outputProducts as $wooitem)
                    {
                        if(Product::where('product_id',$wooitem->id)->first() == null){
                            if(++ $i == 1 + config('app.product_sync_limit')){
                                break;
                            }else{

                                // WooCommerce Product added ProductTable
                                $prodDetails = new Product;
                                $prodDetails->product_id = $wooitem->id;
                                $prodDetails->name = $wooitem->name;
                                $prodDetails->price = number_format($wooitem->price,2);
                                $prodDetails->description = strip_tags($wooitem->description);
                                $prodDetails->save();
                                Log::info('WoooCommerce ProductID: '.$wooitem->id. '('.$wooitem->name.') added');
                            }
                        }
                    }

                }
            }else{
                Log::critical('Invalid Response: ' . $response->status());
            }

        }catch (\Exception $exception){
            Log::critical($exception);
        }
    }
}
