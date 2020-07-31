<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    private function getToken($email, $password)
    {
        $token = null;
        //$credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt(['email'=>$email, 'password'=>$password])) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'Password or email is invalid',
                    'token'=> $token
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'Token creation failed',
            ]);
        }

        return $token;
    }

    public function login(Request $request)
    {

        $user = User::where('email', $request->email)->get()->first();

        if ($user && Hash::check($request->password, $user->password)) // The passwords match...
        {

//            $token = self::getToken($request->email, $request->password);
//            $user->auth_token = $token;
            $user->save();

            $response = ['success'=>true, 'data'=>['id'=>$user->id,'name'=>$user->first_name, 'email'=>$user->email]];
        }
        else
            $response = ['success'=>false, 'data'=>'Record doesnt exists'];

        return response()->json($response, 201);
    }

    public function register(Request $request)
    {

        $payload = [
            'password'=>\Hash::make($request->password),
            'email'=>$request->email,
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'birthday'=>$request->birthday,
            'address'=>$request->address,
            'district'=>$request->district,
            'city'=>$request->city,
            'country'=>$request->country,
            'phone'=>$request->phone,
//            'auth_token'=> ''
        ];

        $user = new User($payload);
        if ($user->save()) {

//            $token = self::getToken($request->email, $request->password); // generate user token

//            if (!is_string($token))  return response()->json(['success'=>false,'data'=>'Token generation failed'], 201);

            $user = User::where('email', $request->email)->get()->first();

//            $user->auth_token = $token; // update user token

            $user->save();

            $response = ['success'=>true, 'data'=>[
                'first_name'=>$user->first_name,
                'id'=>$user->id,
                'email'=>$request->email,
                'last_name'=>$request->last_name,
                'birthday'=>$request->birthday,
                'address'=>$request->address,
                'district'=>$request->district,
                'city'=>$request->city,
                'country'=>$request->country,
                'phone'=>$request->phone,
            ]];
        } else
            $response = ['success'=>false, 'data'=>'Couldnt register user'];


        return response()->json($response, 201);
    }

}