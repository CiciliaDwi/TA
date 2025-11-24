<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('user.listuser', compact('users'));
    }
    public function showLogin()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:5',
            'alamat' => 'required',
            'jabatan' => 'required|in:admin,kasir',
            'gaji' => 'required|numeric',
            'tglLahir' =>'required|date',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'alamat' => $validated['alamat'],
            'jabatan' => $validated['jabatan'],
            'gaji' => $validated['gaji'],
            'tglLahir' => $validated['tglLahir'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->jabatan == 'kasir') {
                return redirect()->route('transactions.index');
            }

            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return back()->withErrors([
            'username' => 'Username atau password salah!'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
    public function edit($id)
    {
        $user = User::find($id);
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->all());

        return redirect()->route('users.index')
            ->with('success', 'Data user berhasil diperbarui');
    }
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete(); // Ini akan langsung menghapus data dari database

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}