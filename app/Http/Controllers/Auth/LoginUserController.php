<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginUserController extends Controller
{
    public function index(){
        return view('auth.login');
    }

    public function store(Request $request){
        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
        ? 'email' 
        : 'username';

        $request->merge([$loginField => $request->input('login')]);

        $credentials = $request->validate([
            'username' => 'nullable|string',
            'email' => 'nullable|string',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials, $request->boolean('remember'))){
            $request->session()->regenerate();
            $user = Auth::user();

            Auth::login($user);

            return redirect()->intended(RouteServiceProvider::HOME);
        }
        return back()->withErrors([
            'login' => 'the provider credentials does not match our records',
        ])->onlyInput('login');
    }
}
