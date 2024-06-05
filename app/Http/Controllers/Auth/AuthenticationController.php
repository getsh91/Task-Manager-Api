<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Validator;


class AuthenticationController extends Controller
{
    //
    public function register(Request $request){
        
        $validatedData = Validator::make($request->all(), [
            'firstName' => 'required|string|min:3',
            'lastName' => 'required|string|min:3',
            'phoneNumber' => 'required|string|min:10',
            'address' => 'required|string|min:3',
            'billNumber' => 'required|string|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|',
        ]);
        if ($validatedData->fails()) {
            return response(['message' => $validatedData->errors()->first()], 422);
        }

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
        
         return response([
            'user'=>$user,
    
            'message'=>'User register successfully'
         ],201);
    }

    public function login(Request $request){
        $validatedData = Validator::make($request->all(), [
            'phoneNumber' => 'required|string|min:10',
            'password' => 'required|min:6|',
        ]);
        if ($validatedData->fails()) {
            return response(['message' => $validatedData->errors()->first()], 422);
        }

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
