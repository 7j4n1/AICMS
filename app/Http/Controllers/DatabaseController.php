<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DatabaseController extends Controller
{
    /**
     * Show the backup page
     */
    public function index()
    {
        // Show the backup page
        // and the list of backups
        $getBackups = $this->list();

        return view('admin.backup.index', [
            'backups' => $getBackups[0],
            'lastBackupTime' => $getBackups[1],
        ]);
    }

    /**
     * Backup the database
     */
    public function backup()
    {
        // Backup database
        // $exit_code = Artisan::call('backup:run --only-db --disable-notifications');

        $output = shell_exec('cd '.base_path().' && php artisan backup:run --only-db --disable-notifications 2>&1');

        // Check if the backup was successful

        if (strpos($output, 'Backup failed') !== false || strpos($output, 'Could not') !== false) {
            return redirect()->route('backup.index')->with('error', 'Database backup failed. '.$output);
        }

        return redirect()->route('backup.index')->with('success', 'Database backup successful. '.$output);
    }

    /**
     * Get the list of all backups from the storage
     *
     * @return array
     */
    public function list()
    {
        // Get all backups file names available in the storage
        $backups = glob(storage_path('app/Laravel/*.zip'));

        // Sort the backups by the latest first
        rsort($backups);

        // get the latest backup file timestamp
        $latestBackup = null;
        $lastBackupTime = null;
        if (count($backups) > 0) {
            $latestBackup = $backups[0];
            $lastBackupTime = date('Y-m-d H:i:s', filemtime($latestBackup));
        }

        $backupInfos = [];
        foreach ($backups as $backup) {
            $backupInfos[] = [
                'file_name' => basename($backup),
                'file_size' => round(filesize($backup) / 1024 / 1024, 2).' MB',
                'created_at' => date('Y-m-d H:i:s', filemtime($backup)),
            ];
        }

        return [$backupInfos, $lastBackupTime];
    }

    /**
     * Download the backup file
     */
    public function download($file)
    {
        // check if the file exists
        if (! file_exists(storage_path('app/Laravel/'.$file))) {
            return redirect()->route('backup.index')->with('error', 'Backup file not found.');
        }

        // Download the backup file
        return response()->download(storage_path('app/Laravel/'.$file));
    }

    /**
     * Delete the backup file
     */
    public function delete($file)
    {
        // check if the file exists
        if (! file_exists(storage_path('app/Laravel/'.$file))) {
            return redirect()->route('backup.index')->with('error', 'Backup file not found.');
        }

        // Delete the backup file
        unlink(storage_path('app/Laravel/'.$file));

        return redirect()->route('backup.index')->with('success', 'Backup file deleted successfully.');
    }

    /**
     * Upload and restore database from backup file
     */
    public function restore(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:512000', // Max 500MB
        ]);

        try {
            $file = $request->file('backup_file');
            $tempPath = storage_path('app/temp');

            // Create temp directory if it doesn't exist
            if (! file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            // Move uploaded file to temp directory
            $uploadedFile = $file->move($tempPath, 'restore_'.time().'.zip');

            // Extract the zip file
            $zip = new ZipArchive;
            if ($zip->open($uploadedFile) === true) {
                $zip->extractTo($tempPath);
                $zip->close();

                // Find the SQL file in the extracted contents
                $sqlFile = null;
                $files = glob($tempPath.'/db-dumps/*.sql');

                if (count($files) > 0) {
                    $sqlFile = $files[0];
                } else {
                    // Clean up
                    $this->cleanupTempFiles($tempPath);

                    return redirect()->route('backup.index')
                        ->with('error', 'No SQL file found in the backup archive.');
                }

                // Read SQL file content
                $sql = file_get_contents($sqlFile);

                // Execute SQL restore with foreign key checks disabled
                DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');

                // Split SQL into individual statements and execute
                $statements = array_filter(
                    array_map('trim', explode(';', $sql)),
                    function ($statement) {
                        return ! empty($statement);
                    }
                );

                foreach ($statements as $statement) {
                    if (! empty($statement)) {
                        // Statement already has semicolon from split, no need to add
                        DB::unprepared($statement);
                    }
                }

                DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');

                // Clean up temp files
                $this->cleanupTempFiles($tempPath);

                return redirect()->route('backup.index')
                    ->with('success', 'Database restored successfully from backup file.');

            } else {
                // Clean up
                if (file_exists($uploadedFile)) {
                    unlink($uploadedFile);
                }

                return redirect()->route('backup.index')
                    ->with('error', 'Failed to extract backup file. The file may be corrupted.');
            }

        } catch (\Exception $e) {
            // Clean up on error
            if (isset($tempPath)) {
                $this->cleanupTempFiles($tempPath);
            }

            return redirect()->route('backup.index')
                ->with('error', 'Database restore failed: '.$e->getMessage());
        }
    }

    /**
     * Clean up temporary files
     */
    private function cleanupTempFiles($tempPath)
    {
        if (file_exists($tempPath)) {
            // Remove all files and subdirectories in temp path
            $files = glob($tempPath.'/{,.}*', GLOB_BRACE);
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                } elseif (is_dir($file) && ! in_array(basename($file), ['.', '..'])) {
                    $this->deleteDirectory($file);
                }
            }
        }
    }

    /**
     * Recursively delete a directory
     */
    private function deleteDirectory($dir)
    {
        if (! file_exists($dir)) {
            return true;
        }

        if (! is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (! $this->deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
}
