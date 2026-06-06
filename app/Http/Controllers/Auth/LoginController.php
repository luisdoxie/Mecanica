<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->rol);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials + ['activo' => true], $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Las credenciales no son correctas o tu cuenta está desactivada.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        ActivityLogger::log('Inicio de sesión', 'Auth', Auth::id(), [], ['rol' => Auth::user()->rol]);

        return $this->redirectByRole(Auth::user()->rol);
    }

    public function logout(Request $request)
    {
        ActivityLogger::log('Cierre de sesión', 'Auth', Auth::id());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(string $rol)
    {
        return match ($rol) {
            'SUPER_ADMIN' => redirect()->route('admin.dashboard'),
            'GERENTE'     => redirect()->route('gerente.dashboard'),
            'MECANICO'    => redirect()->route('mecanico.dashboard'),
            'CLIENTE'     => redirect()->route('cliente.vehiculo'),
            default       => redirect('/'),
        };
    }
}
