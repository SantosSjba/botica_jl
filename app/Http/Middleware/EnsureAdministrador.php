<?php

namespace App\Http\Middleware;

use App\Helpers\PermisosHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rutas solo para ADMINISTRADOR (igual que sistema antiguo).
 * Aplicar a: mantenimiento, compras, reportes, backup, configuración, acerca.
 */
class EnsureAdministrador
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!PermisosHelper::isAdministrador()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'No tiene permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
