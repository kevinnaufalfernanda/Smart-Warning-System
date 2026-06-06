<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function processLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|ends_with:@gmail.com',
            'password' => [
                'required', 
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ]
        ], [
            'email.ends_with' => 'Email harus menggunakan domain @gmail.com yang valid.',
            'password' => 'Format password tidak valid (Harus mengandung huruf besar, kecil, angka, dan simbol).'
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('register');
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255', 
                'regex:/^[A-Z][a-zA-Z]*(?:\s[A-Z][a-zA-Z]*)*$/'
            ],
            'email' => 'required|string|email|max:255|unique:users|ends_with:@gmail.com',
            'password' => [
                'required', 
                'string', 
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
        ], [
            'name.regex' => 'Nama hanya boleh berisi huruf dan SETIAP KATA harus diawali dengan huruf kapital (Contoh: Budi Santoso).',
            'email.ends_with' => 'Email harus menggunakan alamat asli dari @gmail.com.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Warga',
            'phone_number' => '-' // default fallback since not in form
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login dengan akun yang baru saja Anda buat.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
