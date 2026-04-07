<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermiso
{
    /**
     * Verifica que el rol del usuario tenga acceso a la ruta solicitada.
     * CERO consultas a base de datos: solo lee session('permisos').
     *
     * Si la ruta no está mapeada en config('opcionesmenu'), se permite el acceso
     * (rutas internas sin restricción de menú).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $permisos = session('permisos', []);
        $rutaNombre = $request->route()?->getName();

        $opcionId = $this->resolverOpcionMenu($rutaNombre);

        // Si la ruta tiene una opción de menú asociada y el rol no la tiene → 403
        if ($opcionId !== null && !in_array($opcionId, $permisos, strict: true)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }

    /**
     * Mapea el nombre de ruta a un ID de opción de menú desde config/opcionesmenu.php.
     * Retorna null si la ruta no requiere control de acceso por menú.
     */
    private function resolverOpcionMenu(?string $rutaNombre): ?int
    {
        if ($rutaNombre === null || !config()->has('opcionesmenu')) {
            return null;
        }

        // Buscar si alguna clave de config('opcionesmenu') coincide con la ruta
        foreach (config('opcionesmenu', []) as $opcionId) {
            // La convención es que la config mapea clave→id
            // El mapeo ruta→opcion se hace en config/rutasopcion.php si existe
        }

        // Mapeo directo ruta → opcionmenu_id usando config/rutasopcion.php
        $mapa = config('rutasopcion', []);

        return $mapa[$rutaNombre] ?? null;
    }
}
