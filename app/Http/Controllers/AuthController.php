<?php

namespace App\Http\Controllers;

use App\Models\Administrators;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "full_name" => "required",
            "username" => ["required","min:3","unique:users,username","regex:/^[A-Za-z0-9._]+$/"],
            "password" => ["required", "min:6"],
        ]);

        if($validator->fails()){
            return $this->validateFails($validator->errors());
        }

        $user = User::create($request->all());
        $user["role"] = "user";
        return $this->createApi([
            "status" => "success",
            "message" => "user Registration successful",
            "data" => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => ["required"],
            "password" => ["required"],
        ]);

        if($validator->fails()){
            return $this->validateFails($validator->errors());
        }

        $role = "user";
        $user = User::whereUsername($request->username)->first();

        if(is_null($user)){
            $admin = Administrators::whereUsername($request->username)->first();

            if(is_null($admin)){
                return $this->createApi([
                    "status" => "authentication_failed",
                    "message" => "The username or password you entered is incorrect"
                ], 403);
            }

            $role = "admin";
            $user = $admin;
        }

        if(!$user || !Hash::check($request->password, $user->password)){
            return $this->createApi([
                "status" => "authentication_failed",
                "message" => "The username or password you entered is incorrect"
            ], 403); 
        }

        $token = $user->createToken("auth_token")->plainTextToken;
        $user["role"] = $role;
        $user["token"] = $token;

        return $this->createApi([
            "status" =>"success",
            "message" =>"Login successful",
            "data" => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard("user")->user();
        if(!is_null($user)) $user->tokens()->delete();

        $admin = Auth::guard("admin")->user();
        if(!is_null($admin)) $admin->tokens()->delete();

        return response()->json([
            "status" => "success",
            "message" => "Logout successful"
        ], 200);

    }
}
