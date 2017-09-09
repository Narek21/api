<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use GuzzleHttp\Client;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVehicles( $year=null, $manufacturer=null, $model=null, Request $request ){

        $no_result =[
            'Count' => 0,
            'Result'=> []
        ];
        if( !$year || !is_numeric($year) || !$manufacturer || !$model )
        {

            return Response::json($no_result, 200);
        }else{
            $apiURL = 'https://one.nhtsa.gov/webapi/api/SafetyRatings/';
            $pathURL = 'modelyear/'.$year.'/make/'.$manufacturer.'/model/'.$model.'?format=json';
            try{

                $client = new Client();
                $res = $client->get($apiURL.$pathURL);
                $res = json_decode($res->getBody()->getContents(),true);
                if($res['Count'] == 0){
                    return Response::json($no_result, 200);
                }else{
                    $result=[
                        'Count'=>$res['Count'],
                        'Results'=>[]
                    ];
                    if(isset($request['withRating']) && $request['withRating']=='true'){

                        foreach ($res['Results'] as $val){
                            $pathURL = 'VehicleId/'.$val['VehicleId'].'?format=json';
                            try{
                                $client = new Client();
                                $res = $client->get($apiURL.$pathURL);
                                $res = json_decode($res->getBody()->getContents(),true);
                                if(isset($res['Results']) && isset($res['Results'][0]['OverallRating'])){
                                    $result['Results'] []=[
                                        'CrashRating'=> $res['Results'][0]['OverallRating'],
                                        'Description'=>$val['VehicleDescription'],
                                        'VehicleId'=>$val['VehicleId'],
                                    ];
                                }else{
                                    $result['Results'] []=[
                                        'CrashRating'=> "Not Rated",
                                        'Description'=>$val['VehicleDescription'],
                                        'VehicleId'=>$val['VehicleId'],
                                    ];
                                }
                            }catch (\Exception $e){
                                $result['Results'] []=[
                                    'CrashRating'=> "Not Rated",
                                    'Description'=>$val['VehicleDescription'],
                                    'VehicleId'=>$val['VehicleId'],
                                ];
                            }
                        }

                    }else{
                        foreach ($res['Results'] as $val){
                            $result['Results'] []=[
                                'Description'=>$val['VehicleDescription'],
                                'VehicleId'=>$val['VehicleId'],
                            ];
                        }
                    }
                    return Response::json($result, 200);
                }

            }catch (\Exception $e){

                return Response::json($no_result, 200);
            }
        }
    }

    public function postVehicles( Request $request )
    {

        $no_result =[
            'Count' => 0,
            'Result'=> []
        ];
        if( !$request->isJson() || !$request['modelYear'] || !is_numeric($request['modelYear']) || !$request['manufacturer'] || !$request['model'] )
        {

            return Response::json($no_result, 200);
        }else{

            $apiURL = 'https://one.nhtsa.gov/webapi/api/SafetyRatings/';
            $pathURL = 'modelyear/'.$request['modelYear'].'/make/'.$request['manufacturer'].'/model/'.$request['model'].'?format=json';
            try{
                $client = new Client();
                $res = $client->get($apiURL.$pathURL);
                $res = json_decode($res->getBody()->getContents(),true);
                if($res['Count'] == 0){
                    return Response::json($no_result, 200);
                }else{
                    $result=[
                        'Count'=>$res['Count'],
                        'Results'=>[]
                    ];
                    $result['Count'] = $res['Count'];
                    foreach ($res['Results'] as $val){
                        $result['Results'] []=[
                            'Description'=>$val['VehicleDescription'],
                            'VehicleId'=>$val['VehicleId'],
                        ];
                    }
                    return Response::json($result, 200);
                }

            }catch (\Exception $e){
                return Response::json($no_result, 200);
            }
        }
    }
}
