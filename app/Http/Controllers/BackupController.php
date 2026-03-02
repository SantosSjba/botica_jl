<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BackupController extends Controller
{
    /**
     * Página de backup: crear copia de respaldo de la base de datos.
     * Solo administrador (ruta protegida por middleware).
     */
    public function index(): View
    {
        return view('pages.backup.index', [
            'title' => 'Backup',
        ]);
    }

    /**
     * Generar y descargar dump SQL de la base de datos actual.
     * Usa la conexión configurada en .env (no credenciales en BD).
     */
    public function download(Request $request): Response
    {
        set_time_limit(600);

        $driver = config('database.default');
        if ($driver !== 'mysql') {
            abort(422, 'El backup SQL solo está disponible para MySQL.');
        }

        $dbName = config('database.connections.mysql.database');
        $tablesKey = 'Tables_in_' . $dbName;

        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(fn ($r) => $r->{$tablesKey}, $tables);

        $sql = $this->buildHeader($dbName);

        foreach ($tableNames as $table) {
            $sql .= $this->exportTable($table);
        }

        $sql .= "\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n";
        $sql .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n";
        $sql .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\r\n";

        $filename = $dbName . '_backup_' . date('Y-m-d_His') . '.sql';

        return response($sql, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($sql),
        ]);
    }

    protected function buildHeader(string $dbName): string
    {
        return "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\n"
            . "SET time_zone = \"+00:00\";\r\n\r\n"
            . "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n"
            . "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n"
            . "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n"
            . "/*!40101 SET NAMES utf8 */;\r\n--\r\n-- Database: `{$dbName}`\r\n--\r\n\r\n";
    }

    protected function exportTable(string $table): string
    {
        $create = DB::select('SHOW CREATE TABLE `' . $table . '`');
        if (empty($create)) {
            return '';
        }
        $row = (array) $create[0];
        $createSql = end($row) ?: '';
        $out = "\n\n" . $createSql . ";\n\n";

        $rows = DB::table($table)->get();
        if ($rows->isEmpty()) {
            return $out;
        }

        $columns = array_keys((array) $rows->first());
        $chunk = [];
        $count = 0;
        $total = $rows->count();

        foreach ($rows as $row) {
            $rowArr = (array) $row;
            $values = [];
            foreach ($columns as $col) {
                $v = $rowArr[$col] ?? null;
                $values[] = $v === null ? 'NULL' : '"' . addslashes(str_replace(["\r", "\n"], ["\\r", "\\n"], (string) $v)) . '"';
            }
            $chunk[] = "\n(" . implode(',', $values) . ")";
            $count++;
            if ($count % 100 === 0 || $count === $total) {
                $out .= "INSERT INTO `{$table}` (`" . implode('`,`', $columns) . "`) VALUES " . implode(',', $chunk) . ";\n";
                $chunk = [];
            }
        }
        if (!empty($chunk)) {
            $out .= "INSERT INTO `{$table}` (`" . implode('`,`', $columns) . "`) VALUES " . implode(',', $chunk) . ";\n";
        }

        return $out . "\n\n";
    }
}
