<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ExcelDownloadController extends Controller
{
    public function downloadExcel(Request $request)
    {
        $type = $request->get('type');
        $batch = $request->get('batch');

        if (!$type || !$batch) {
            abort(404, 'Invalid download parameters');
        }

        // Get the file path from cache
        $cacheKey = "excel_report_{$type}_{$batch}";
        $filePath = Cache::get($cacheKey, "reports/csv_import_{$type}_{$batch}.xlsx");
        
        Log::info('Download request', [
            'type' => $type,
            'batch' => $batch,
            'cache_key' => $cacheKey,
            'file_path' => $filePath
        ]);

        if (!$filePath) {
            Log::error('File path not found in cache', [
                'cache_key' => $cacheKey,
                'available_keys' => array_keys(Cache::getStore() ?? [])
            ]);
            abort(404, 'Report file not found or has expired');
        }

        // Check if file exists using the correct Storage disk method
        if (!Storage::disk('public')->exists($filePath)) {
            Log::error('File does not exist', [
                'file_path' => $filePath,
                'full_path' => storage_path('app/public/' . $filePath)
            ]);
            abort(404, 'Report file not found');
        }

        $filename = basename($filePath);
        $fullPath = storage_path('app/public/' . $filePath);

        
        return response()->download($fullPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
