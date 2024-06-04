<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;


class AuthenticationController extends Controller
{
    //
    public function register(RegisterRequest $request){
        $request->validated();

        $userdata=[
            'firstName'=>$request->firstName,
            'lastName'=>$request->lastName,
            'email'=>$request->email,
            'phoneNumber'=>$request->phoneNumber,
            'address'=>$request->address,
            'billNumber'=>$request->billNumber,
            'password'=>Hash::make($request->password)
        ];
        $user=User::create($userdata);
        $token=$user->createToken('auth_token')->plainTextToken;
         return response([
            'user'=>$user,
            'token'=>$token,
            'message'=>'User register successfully'
         ],201);
    }

    public function login(LoginRequest $request){
        $request->validated();
        $user=User::where('phoneNumber',$request->phoneNumber)->first();
        if(!$user || !Hash::check($request->password,$user->password)){
            return response([
                'message'=>'Invalid credentials'
            ],422);
        }
        $token=$user->createToken('auth_token')->plainTextToken;
        return response([
            'user'=>$user,
            'token'=>$token,
            'message'=>'User login successfully'
        ],200);
    }
}
