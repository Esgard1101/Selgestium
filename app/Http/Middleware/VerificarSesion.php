<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificarSesion
{
    /**
     * Verifica que la sesión esté enriquecida con los datos de rol y sucursal.
     * Si faltan, cierra sesión y redirige al login.
     *
     * Este middleware se apila DESPUÉS de 'auth' de Fortify/Jetstream.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('perfil_id') || !session()->has('sucursal_id')) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('status', 'Tu sesión expiró. Por favor vuelve a iniciar sesión.');
        }

        return $next($request);
    }
}
