<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){

        // Check Validation name, email and password
        // Everhing required and password min 8 charecters, email should be unique
        $validator = Validator::make($request->all(), [
            'name'=>'required|string',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:8'
        ]);

        // if validation fails return response with errors
        if ($validator->fails()) {
            $returnResponseData = [
                'error' => 'bad_request',
                'message' => $validator->errors()
            ];
            return response()->json($returnResponseData,400);
        }

        // User Creating function
        // Password convert to hash
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create bearer token to created user
        $token = $user->createToken('authToken')->plainTextToken;

        // Return Reponse data with barer token
        $returnResponseData = [
            'access_token' => $token,
            'token_type' => 'bearer'
        ];
        return response()->json($returnResponseData,200);
    }



    public function get_method_invalid()
    {
        // I create get method form every post method for when someone
        // visit endpoint via get request showing this error message

        $returnResponseData = [
            'error' => 'invalid_method',
            'message' => 'GET method not supporting this api'
        ];
        return response()->json($returnResponseData,405);
    }


    // This is login function
    // after create user, you can login and return bearer token

    public function login(Request $request){
        if (!Auth::attempt($request->only('email', 'password'))) {

            //Invalid email and password showing this message
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'Login information invalid',
            ], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('authToken')->plainTextToken;

        // After validate your email and password, return response with bearer token
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

}
