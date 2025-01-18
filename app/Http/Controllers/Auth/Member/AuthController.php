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

    public function getUser(){
        $user = Auth::user();
        return response()->json($user);
    }
    
    public function getUserProfile(){
        $user = Auth::user();
        $user->load(['userProfile', 'faculty.university']);
        return response()->json($user);
    }

    public function register(UserRequest $request){
        try{
            $user = new User([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user->assignRole(UserRole::MEMBER->value);
            $user->role = 'member';
            $user->save();

            return response()->json(['message' => 'Register Successfully', 'user' => $user], 201);
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
                'identifier' => ['Wrong Email or Username'],
                'password' => ['Wrong Password']
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

    public function createUserImage(Request $request){

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    
        $filePath = $request->file('user_profile')->store('profiles', 'public');
    
        if ($user->userProfile) {
            $user->userProfile->update([
                'file_name' => $request->file('user_profile')->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $request->file('user_profile')->getSize(),
            ]);
        } else {
            $user->userProfile()->create([
                'file_name' => $request->file('user_profile')->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $request->file('user_profile')->getSize(),
            ]);
        }
    
        return response()->json([
            'message' => 'User Image Successfully Uploaded',
            'user' => $user,
        ], 201);

        return response()->json(['message' => 'User Image Success Uploaded', 'user' => $user], 201);
    }

    public function updateProfile(Request $request){
    if (!Auth::check()) {
        return response()->json(['message' => 'User not authenticated'], 401);
    }

    $request->validate([
        'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
        'faculty_id' => 'nullable|exists:faculties,id',
    ]);

    $user = Auth::user();

    if ($user) {
        $user->update([
            'username' => $request->username,
        ]);

        if ($user) {
            $user->update([
                'faculty_id' => $request->faculty_id,
            ]);
        } else {
            $user->create([
                'faculty_id' => $request->faculty_id,
            ]);
        }

        return response()->json([
            'message' => 'Username and Faculty updated successfully',
            'user' => $user->load('userProfile'),
        ], 200);
    }

    return response()->json(['message' => 'User not found'], 404);
}}