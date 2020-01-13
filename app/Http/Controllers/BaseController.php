<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller as Controller;
use App\User;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message = '')
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code = 404, $errorMessages = [])
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

    /**
     * get current authenticated user
     * 
     * @return \App\User | boolean
     */

     public function getAuthenticatedUser() {
         try {
             $u = Auth::user();
             $user = User::with("roles")->find($u->id);
             return $user;
         }
         catch(Exception $e) {
             return false;
         }
     }

}