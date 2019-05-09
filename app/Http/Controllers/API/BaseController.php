<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    /**
     * return success response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message, $commentsStatus=false, $commentsArr=array())
    {
        if($commentsStatus==false){
        	$response = [
                'success' => true,
                'data'    => $result,
                'message' => $message,
            ];
        }else{
            $response = [
                'success' => true,
                'data'    => $result,
                'comments'=> $commentsArr,
                'message' => $message,
            ];
        }

        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}