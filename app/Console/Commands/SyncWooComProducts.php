<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\ApiResponse;
use App\Models\Product;
use App\Jobs\SyncWooComProduct;
class SyncWooComProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:wooproducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync WooCommerce Products from API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Starting Synchrnoize Command

        $this->info('WooCommerce Product Synchronize...');
        Log::info('WooCommerce Product Synchronizing');

        // Get last Response Details
        $getlastResponse = ApiResponse::getLastEndpointDetails(config('app.woocom_endpoint'));

        // Check availble last respond data
        if($getlastResponse){
            Log::info('Found latest response: '.config('app.woocom_endpoint'));
            Log::info('Found latest response: '.config('app.woocom_endpoint'));
            Log::info('Last response time (sec): '. number_format($getlastResponse->response_time,2));
            $this->info('Latest Response Time: '. number_format($getlastResponse->response_time,2));

            // Check Respond Time < 1
            if($getlastResponse->response_time < 1){
                $this->info('Sync Queue Job Starting: 1 Minutes');
                $this->info('Sync Queue Job Starting Time: '.now()->addMinute(1) );
                Log::info('Queue Job Starting: '. '1 Minutes');

                //Run Queue after 1 min
                SyncWooComProduct::dispatch(SyncWooComProduct::class)->delay(now()->addMinute(1));
            }else{
                Log::info('Queue Job Starting: '. '5 Minutes');
                $this->info('Sync Queue Job Starting: 5 Minutes');
                $this->info('Sync Queue Job Starting: '.now()->addMinute(5) );

                //Run Queue job after 5 min
                SyncWooComProduct::dispatch(SyncWooComProduct::class)->delay(now()->addMinute(5));
            }

        }else{
            //If Respond details not avaible (first time respond)
            Log::info('Latest response not found');
            $this->info('Latest Response time not found');
            $this->info('Sync Queue Starting');
            Log::info('Queue Job Starting: '.'Now');
            SyncWooComProduct::dispatch(SyncWooComProduct::class);
        }
    }
}
