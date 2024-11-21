<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AuthController extends Controller{

    public function index(): View
    {
        return view('auth.admin.index');
    }
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],

            'password' => ['required', 'confirmed', Rules\Password::defaults()],

        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->save();
        
        if (Auth::check() && Auth::user()->hasRole(UserRole::SUPER_ADMIN->value)) {
            $user->assignRole(UserRole::ADMIN->value);  
        } else {
            $user->assignRole(UserRole::MEMBER->value);
        }

        event(new Registered($user));

        return redirect()->intended(RouteServiceProvider::HOME);
    }

}

