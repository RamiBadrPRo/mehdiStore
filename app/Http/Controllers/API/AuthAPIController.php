<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Hash;
use Validator;
   
class AuthAPIController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('mehdiStore')->accessToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('mehdiStore')->accessToken; 
            $success['name'] =  $user->name;
            $success["roles"] = $user->getRoleNames();
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.',403, ['error'=>'Unauthorised']);
        } 
    }

    public function getAuthUser(Request $request) {
        $user = $this->getAuthenticatedUser();
        if(!$user) {
            return $this->sendError("user.unauthorized",403);
        }

        
        return $this->sendResponse($user,"");
    }
}