<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseBackupController extends Controller
{
    public function backup()
    {
        try {
            $filename = 'backup_'.now()->format('Y-m-d_H-i-s').'.sql';
            $backupPath = storage_path('app/backups/'.$filename);

            // Crear directorio si no existe
            if (! file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            // Ejecutar mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg(config('database.connections.mysql.username')),
                escapeshellarg(config('database.connections.mysql.password')),
                escapeshellarg(config('database.connections.mysql.host')),
                escapeshellarg(config('database.connections.mysql.database')),
                escapeshellarg($backupPath)
            );

            $output = shell_exec($command.' 2>&1');

            if (file_exists($backupPath)) {
                return redirect()
                    ->route('admin.settings.edit')
                    ->with('success', 'Respaldo creado exitosamente: '.$filename);
            } else {
                return redirect()
                    ->route('admin.settings.edit')
                    ->with('error', 'Error al crear el respaldo: '.$output);
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.edit')
                ->with('error', 'Error al crear respaldo: '.$e->getMessage());
        }
    }

    public function showRestore()
    {
        $backupDir = storage_path('app/backups');
        $backups = [];

        if (file_exists($backupDir)) {
            $files = array_diff(scandir($backupDir), ['.', '..']);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $backups[] = [
                        'filename' => $file,
                        'size' => filesize($backupDir.'/'.$file),
                        'date' => filemtime($backupDir.'/'.$file),
                    ];
                }
            }
            rsort($backups);
        }

        return view('admin.database.restore', compact('backups'));
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string',
            'confirmation' => 'required|accepted',
        ]);

        $backupPath = storage_path('app/backups/'.$request->backup_file);

        if (! file_exists($backupPath)) {
            return redirect()
                ->route('admin.database.restore-form')
                ->with('error', 'Archivo de respaldo no encontrado.');
        }

        try {
            // Ejecutar mysql para restaurar
            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s %s < %s',
                escapeshellarg(config('database.connections.mysql.username')),
                escapeshellarg(config('database.connections.mysql.password')),
                escapeshellarg(config('database.connections.mysql.host')),
                escapeshellarg(config('database.connections.mysql.database')),
                escapeshellarg($backupPath)
            );

            $output = shell_exec($command.' 2>&1');

            return redirect()
                ->route('admin.settings.edit')
                ->with('success', 'Base de datos restaurada exitosamente desde: '.$request->backup_file);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.database.restore-form')
                ->with('error', 'Error al restaurar: '.$e->getMessage());
        }
    }

    public function showClearAll()
    {
        return view('admin.database.clear-all');
    }

    public function clearAll(Request $request)
    {
        $request->validate([
            'special_token' => 'required|string',
            'confirmation' => 'required|accepted',
        ]);

        // Verificar token especial
        if ($request->special_token !== config('app.token_special_commands')) {
            return redirect()
                ->route('admin.database.clear-form')
                ->with('error', 'Token de confirmación inválido.');
        }

        try {
            // Desabilitar verificación de claves foráneas
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Obtener todas las tablas
            $tables = DB::select('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?', [
                config('database.connections.mysql.database'),
            ]);

            // Truncar todas las tablas
            foreach ($tables as $table) {
                DB::statement('TRUNCATE TABLE `'.$table->TABLE_NAME.'`');
            }

            // Re-habilitar verificación de claves foráneas
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Crear usuario admin por defecto
            User::create([
                'name' => 'Admin',
                'email' => 'admin@escuela.com',
                'apellido_paterno' => 'Sistema',
                'apellido_materno' => 'Administrador',
                'password' => bcrypt('password'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]);

            return redirect()
                ->route('admin.settings.edit')
                ->with('success', 'Sistema limpiado exitosamente. Usuario admin creado con email: admin@escuela.com y contraseña: password');
        } catch (\Exception $e) {
            // Re-habilitar verificación de claves foráneas en caso de error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()
                ->route('admin.database.clear-form')
                ->with('error', 'Error al limpiar el sistema: '.$e->getMessage());
        }
    }
}
