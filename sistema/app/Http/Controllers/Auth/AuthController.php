<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processa o login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('forcing.index'));
        }

        return back()->withErrors([
            'username' => 'As credenciais fornecidas não conferem com nossos registros.',
        ])->onlyInput('username');
    }

    /**
     * Exibe o formulário de registro
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Processa o registro de novo usuário
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'empresa' => 'required|string|max:255',
            'setor' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'empresa' => $request->empresa,
            'setor' => $request->setor,
            'perfil' => 'user', // perfil padrão
        ]);

        Auth::login($user);

        return redirect()->route('forcing.index')
            ->with('success', 'Conta criada com sucesso!');
    }

    /**
     * Faz logout do usuário
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Logout realizado com sucesso!');
    }
}
