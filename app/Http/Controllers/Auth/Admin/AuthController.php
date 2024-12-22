<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;    
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller{

    public function getAdminProfile(Request $request){
        $user = $request->user();
        return response()->json($user);
    }

    public function register(UserRequest $request){
        try{
            if (!Auth::user()->hasRole('super_admin')) {
                return response()->json(['message' => 'Unauthorized. Only SUPER_ADMIN can create ADMIN users.'], 403);
            }
        
            $user = new User([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->role = 'admin';
            $user->assignRole(UserRole::ADMIN);
            $user->save();
            return response()->json(['message' => 'Admin Created Successfully', 'user' => $user ], 201);
            }
        catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request){
        $user = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required',
        ]);

        $loginField = filter_var($request->input('identifier'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $request->merge([$loginField, $request->input('identifier')]);

        if(!Auth::attemptWhen([$loginField => $request->identifier, 'password' => $request->password], function($user){
            return true;
        })) {
            throw ValidationException::withMessages([
                'identifier' => ['Wrong Email or Username/Password'],
            ]);
        }

        if (Auth::user()->hasRole(UserRole::ADMIN->value)) { 
            $user = $request->user();
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json(['message' => 'Welcome Admin !', 'access_token' => $token, 'token_type' => 'Bearer'], 200);
        }
        else if (Auth::user()->hasRole(UserRole::SUPER_ADMIN->value)){
            $user = $request->user();
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json(['message' => 'Welcome Super Admin !', 'access_token' => $token, 'token_type' => 'Bearer'], 200);
        }
        else{
            return response()->json(['message' => 'You\'re not a admin'], 500);
        }
    }
    
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout Successfully'], 200);
    }
}

