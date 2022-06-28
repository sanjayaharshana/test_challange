<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    public static function startResponse($url,$body,$resSecounds,$status,$startTime,$endTime)
    {
        $responseDetails = new ApiResponse;
        $responseDetails->endpoint_url = $url;
        $responseDetails->response_data = $body;
        $responseDetails->response_time = $resSecounds ;
        $responseDetails->status_code = $status;
        $responseDetails->start_time = $startTime;
        $responseDetails->end_time = $endTime;
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
}
