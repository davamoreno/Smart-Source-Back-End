<?php

namespace App\Http\Controllers\Auth\Member;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller{
    
    public function getUserProfile(Request $request){
        $user = $request->user();
        return response()->json($user);
    }

    public function register(UserRequest $request){
        try{
            $user = new User([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'faculty_id' => $request->faculty_id,
            ]);

            $user->assignRole(UserRole::MEMBER->value);
            $user->role = 'member';
            $user->save();

            if($request->hasFile('user_profile')){
                $filePath = $request->file('user_profile')->store('profiles', 'public');
                $userProfile = new UserProfile([
                    'file_name' => $request->file('user_profile')->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_size' => $request->file('user_profile')->getSize()
                ]);
                $user->userProfile()->save($userProfile);
            }

            return response()->json(['message' => 'Register Successfully', 'user' => $user->load('userProfile')], 201);

        }
        catch(\Exception $e){
            return response()->json(['message' =>  $e->getMessage()], 500);
        }
    }

    public function login(Request $request){
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required',
        ]);
        
        $identifierField = filter_var($request->input('identifier'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $request->merge([$identifierField, $request->input('identifier')]);

        if(!Auth::attemptWhen([$identifierField => $request->identifier, 'password' => $request->password], function($user){
            return true;
        })) {
            throw ValidationException::withMessages([
                'identifier' => ['Wrong Email or Username/Password'],
            ]);
        }

        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'identifier Successfully', 'access_token' => $token, 'token_type' => 'Bearer'], 200);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout Successfully'], 200);
    }
}
