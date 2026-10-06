<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim_nip' => ['required', 'string', 'max:50', 'unique:users'],
            'fakultas' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required']
        ]);

        $user = User::create([
            'name' => $request->name,
            'nim_nip' => $request->nim_nip,
            'fakultas' => $request->fakultas,
            'email' => $request->email,
            'no_whatsapp' => $request->whatsapp,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
