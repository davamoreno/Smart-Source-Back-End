<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutUserController extends Controller{
    public function store(Request $request){
        $user = Auth::user();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerate();

        return redirect(RouteServiceProvider::HOME);
    }
}