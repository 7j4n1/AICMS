<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Artisan;

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
            'lastBackupTime' => $getBackups[1]
        ]);
    }

    /**
     * Backup the database
     */
    public function backup()
    {
        // Backup database
        // $exit_code = Artisan::call('backup:run --only-db --disable-notifications');

        $output = shell_exec('cd ' . base_path() . ' && php artisan backup:run --only-db --disable-notifications 2>&1');

        // Check if the backup was successful

        if (strpos($output, 'Backup failed') !== false || strpos($output, 'Could not') !== false) {
            return redirect()->route('backup.index')->with('error', 'Database backup failed. ' . $output);
        }

        return redirect()->route('backup.index')->with('success', 'Database backup successful. '. $output);
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
                'file_size' => round(filesize($backup) / 1024 / 1024, 2) . ' MB',
                'created_at' => date('Y-m-d H:i:s', filemtime($backup))
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
        if (!file_exists(storage_path('app/Laravel/' . $file))) {
            return redirect()->route('backup.index')->with('error', 'Backup file not found.');
        }
        // Download the backup file
        return response()->download(storage_path('app/Laravel/' . $file));
    }

    /**
     * Delete the backup file
     */
    public function delete($file)
    {
        // check if the file exists
        if (!file_exists(storage_path('app/Laravel/' . $file))) {
            return redirect()->route('backup.index')->with('error', 'Backup file not found.');
        }

        // Delete the backup file
        unlink(storage_path('app/Laravel/' . $file));

        return redirect()->route('backup.index')->with('success', 'Backup file deleted successfully.');
    }
}
