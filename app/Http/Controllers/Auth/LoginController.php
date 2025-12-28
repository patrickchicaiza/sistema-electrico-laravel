<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard'; // Cambia esto

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Sobrescribir método para redirigir según rol
     */
    protected function authenticated(Request $request, $user)
    {
        // Redirigir según rol (opcional - puedes usar solo dashboard)
        if ($user->hasRole('cliente')) {
            return redirect()->route('dashboard');
        } elseif ($user->hasRole('tecnico')) {
            return redirect()->route('dashboard');
        } elseif ($user->hasRole('administrador') || $user->hasRole('super_admin')) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('dashboard'); // Por defecto
    }

    /**
     * Sobrescribir logout para redirigir a login
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login'); // Redirigir a login después de logout
    }
}