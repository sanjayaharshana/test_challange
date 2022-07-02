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
    private $page_number;

    public function __construct($page_number)
    {
        $this->page_number = $page_number;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            //Get Last Response
            $getlastResponse = ApiResponse::getLastEndpointDetails(config('app.woocom_endpoint'));
            //Check Last Request Available
            if($getlastResponse){
                //Check lasted response time < 1
                    if($getlastResponse->response_time < 1){
                        // Waiting response for 60s
                        sleep(60);
                        Log::info('Page Number: '.$this->page_number);
                        // Call API and Get Response
                        $response = ApiResponse::callAPIGetProducts($this->page_number);
                        // Save Product to Database

                        ApiResponse::saveProductFromResponse($response['response']);
                    }else{
                        // Run response for 5min( 300s)
                        sleep(300);
                        Log::info('Page Number: '.$this->page_number);
                        // Call API and get Response
                        $response = ApiResponse::callAPIGetProducts($this->page_number);
                        //Save product to Database;
                        ApiResponse::saveProductFromResponse($response['response']);
                    }

            }else{
                // If First time Sync Data/ Wihtout History response
                Log::info('Page Number: '.$this->page_number);
                $response = ApiResponse::callAPIGetProducts(1);
                ApiResponse::saveProductFromResponse($response['response']);
            }
        }catch (\Exception $exception){
            // If something wrong run again
           $this->retryUntil();
            Log::critical($exception);
        }


    }

    public function failed()
    {
        // ... what exception was thrown? ...

    }

    public function retryUntil()
    {
        return now()->addMinutes(10);
    }
}
