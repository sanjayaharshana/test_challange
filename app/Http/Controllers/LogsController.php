<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\File;
class LogsController extends Controller
{
    public function index()
    {
        try{
            $file = fopen(storage_path("logs/laravel.log"), "r");
            $a = 1;
            $outArray = [];
            while ($line = fgets($file)) {

                $getErrorDetails = preg_match_all("/Error/i", $line);
                if($getErrorDetails == 1){
                    $errorLog = [
                        'log_type' => 'error',
                        'log_line' => $line,
                    ];
                    array_push($outArray,$errorLog);
                }

                $getInfoDetails = preg_match_all("/Info/i", $line);
                if($getInfoDetails == 1){
                    $errorLog = [
                        'log_type' => 'Info',
                        'log_line' => $line,
                    ];
                    array_push($outArray,$errorLog);
                }
                $a++;
            }
            fclose($file);
            return response()->json($outArray,200);
        }catch (\Exception $exception){
            $returnResponseData = [
                'error' => 'internal_server_error',
                'message' => 'Internal Server Error, Cannot read logfile'
            ];
            return response()->json($returnResponseData,405);
        }


    }
}
