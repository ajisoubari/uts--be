<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // validation params
        $validator = Validator::make($request->all(),[
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }
        // add data to user table
        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password)
         ]);
         // create token
        $token = $user->createToken('auth_token')->plainTextToken;
        // set success response
        $response = [
            'meta' => [
                'code' => '201',
                'message' => 'Register has successfully'
            ],
            'data' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ];

        return response()->json($response, 201);
    }

    public function login(Request $request)
    {
        // validation email or password is wrong
        if (!Auth::attempt($request->only('email', 'password')))
        {
            $response = [
                'meta' => [
                    'code' => '401',
                    'message' => 'Email or passwors is wring'
                ]
            ];
            return response()->json($response, 401);
        }
        // get user data
        $user = User::where('email', $request['email'])->firstOrFail();
        // get token
        $token = $user->createToken('auth_token')->plainTextToken;
        // set success response
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Login has successfully',
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ];
        return response()->json($response, 200);
    }

    public function logout()
    {
        // delete token
        auth()->user()->tokens()->delete();
        //success response
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'You have successfully logged out and the token was successfully deleted'
            ]
        ];
        
        return response()->json($response, 200);
    }
}