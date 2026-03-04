<?php

namespace App\Helpers;

use App\Models\CajaApertura;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

/**
 * Centraliza reglas de permisos y roles del sistema (sistema antiguo).
 * - ADMINISTRADOR: acceso a todo (mantenimiento, compras, reportes, backup, configuración, acerca).
 * - USUARIO: inicio, consultas, clientes (un enlace), ventas (si caja abierta), caja.
 */
class PermisosHelper
{
    public static function tipo(): ?string
    {
        $user = Auth::user();
        if (!$user || !isset($user->tipo)) {
            return null;
        }
        $t = $user->tipo;
        return ($t === 'ADMINISTRADOR' || $t === 'USUARIO') ? $t : null;
    }

    public static function isAdministrador(): bool
    {
        return self::tipo() === 'ADMINISTRADOR';
    }

    public static function isUsuario(): bool
    {
        return self::tipo() === 'USUARIO';
    }

    /**
     * Estado de caja del usuario actual (mismo criterio que sistema antiguo).
     * Primero busca una caja ABIERTA de cualquier día; si no hay, usa la última del día actual.
     */
    public static function estadoCaja(): string
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return '';
        }
        $usu = $usuario->usuario ?? null;
        if ($usu === null || $usu === '') {
            $model = Usuario::find($usuario->getAuthIdentifier());
            $usu = $model ? $model->usuario : null;
        }
        if ($usu === null || $usu === '') {
            return '';
        }
        $usu = (string) $usu;
        $hoy = now()->toDateString();

        $abierta = CajaApertura::where('usuario', $usu)
            ->where('estado', 'Abierto')
            ->orderByDesc('fecha')
            ->orderByDesc('idcaja_a')
            ->first();
        if ($abierta) {
            return 'Abierto';
        }
        $hoyCaja = CajaApertura::where('usuario', $usu)
            ->where('fecha', $hoy)
            ->orderByDesc('idcaja_a')
            ->first();
        return $hoyCaja ? ($hoyCaja->estado ?? '') : '';
    }

    public static function tieneCajaAbierta(): bool
    {
        return self::estadoCaja() === 'Abierto';
    }

    /**
     * Si el menú "Ventas" debe mostrarse (solo cuando caja abierta).
     */
    public static function puedeVerMenuVentas(): bool
    {
        return config('permisos_farmacia.ventas_requiere_caja_abierta', true)
            ? self::tieneCajaAbierta()
            : true;
    }

    /**
     * Subítems de Caja: Apertura solo si no hay caja abierta.
     */
    public static function puedeVerCajaApertura(): bool
    {
        return !self::tieneCajaAbierta();
    }

    /**
     * Subítems de Caja: Cierre solo si hay caja abierta.
     */
    public static function puedeVerCajaCierre(): bool
    {
        return self::tieneCajaAbierta();
    }

    /**
     * Seguimiento de caja: ADMIN siempre; USUARIO solo si tiene caja abierta o cerrada (hoy).
     */
    public static function puedeVerCajaSeguimiento(): bool
    {
        if (self::isAdministrador()) {
            return true;
        }
        $estado = self::estadoCaja();
        return $estado === 'Abierto' || $estado === 'Cerrado';
    }
}
