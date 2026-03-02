<?php

namespace App\Helpers;

/**
 * Convierte un número a su representación en letras (español, soles).
 */
class NumeroALetras
{
    public static function cantidadEnSoles(float $numero): string
    {
        $entero = (int) floor($numero);
        $centavos = (int) round(($numero - $entero) * 100);
        $letras = self::enteroALetras($entero);
        if ($centavos > 0) {
            $letras .= ' CON ' . $centavos . '/100';
        }
        return $letras . ' SOLES';
    }

    private static function enteroALetras(int $n): string
    {
        if ($n === 0) {
            return 'CERO';
        }
        $unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $especiales = ['DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
        $decenas = ['', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

        if ($n < 10) {
            return $unidades[$n];
        }
        if ($n < 20) {
            return $especiales[$n - 10];
        }
        if ($n < 100) {
            $d = (int) floor($n / 10);
            $u = $n % 10;
            $s = $decenas[$d];
            if ($u > 0) {
                $s .= ($d === 2 ? ' ' : ' Y ') . $unidades[$u];
            }
            return $s;
        }
        if ($n < 1000) {
            $c = (int) floor($n / 100);
            $resto = $n % 100;
            if ($n === 100) {
                return 'CIEN';
            }
            return $centenas[$c] . ($resto > 0 ? ' ' . self::enteroALetras($resto) : '');
        }
        if ($n < 1000000) {
            $miles = (int) floor($n / 1000);
            $resto = $n % 1000;
            $s = $miles === 1 ? 'MIL' : self::enteroALetras($miles) . ' MIL';
            return $s . ($resto > 0 ? ' ' . self::enteroALetras($resto) : '');
        }
        if ($n < 1000000000) {
            $millones = (int) floor($n / 1000000);
            $resto = $n % 1000000;
            $s = $millones === 1 ? 'UN MILLÓN' : self::enteroALetras($millones) . ' MILLONES';
            return $s . ($resto > 0 ? ' ' . self::enteroALetras($resto) : '');
        }
        return (string) $n;
    }
}
