<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function index()
    {
        return view('auth.index');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:45'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['username' => 'Username atau password tidak sesuai.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegister()
    {
        $roles = DB::select(
            'SELECT *
            from role_vu
            order by peran'
        );

        return view('auth.regis', compact('roles'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:45'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nomor_peran' => ['required', 'integer'],
        ]);

        $useraktif = DB::selectOne(
            'SELECT nomor_pengguna
            from user_vu
            where nama = ?', [$validated['username']]
        );

        if ($useraktif !== null) {
            return back()
                ->withErrors(['username' => 'Username sudah digunakan, silahkan gunakan username lain'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $role = DB::selectOne(
            'SELECT nomor_peran
            from role_vu
            where nomor_peran=?', [$validated['nomor_peran']]
        );

        if ($role === null) {
            return back()
                ->withErrors(['nomor_peran' => 'anda harus memilih role.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $passwordHashed = Hash::make($validated['password']);

        DB::insert(
            'INSERT INTO v_db_user (nama, sandi, nomor_peran)
        VALUES (?, ?, ?)',
            [$validated['username'], $passwordHashed, $role->nomor_peran]
        );

        return redirect()
            ->route('user.index')
            ->with('success', 'Akun pengguna berhasil dibuat.');
    }
}
