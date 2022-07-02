<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\ApiResponse;
use App\Models\Product;
use App\Jobs\SyncWooComProduct;
use Illuminate\Support\Facades\Artisan;
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

        try {
            //Get Starting Time

            // Get recent response from woocommerce
            $getlastResponse = ApiResponse::getLastEndpointDetails(config('app.woocom_endpoint'));
            Log::info('Get last Response ID:'.$getlastResponse);

            //Check last response is avaible
            if ($getlastResponse) {
                // Loop Pages from total page count
                for ($x = $getlastResponse->page_number + 1; $x <= $getlastResponse->total_page_count; $x++) {
                    // Excute Queue Job with Last Response
                    if($getlastResponse->total_page_count != $getlastResponse->page_number){
                        SyncWooComProduct::dispatch($x);
                    }

                }
            } else {
                // Excute Queue Job Fist time
                SyncWooComProduct::dispatch(1);
                sleep(15);
                $exitCode = Artisan::call('sync:wooproducts');
                $this->info('Recalling');
            }
        }catch (\Exception $exception){
            $this->info('Something Wrong');
            $this->info($exception);
            Log::critical($exception);
        }

    }
}
