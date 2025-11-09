<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SystemConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SystemConfigurationController extends Controller
{
    /**
     * Get all system configurations
     */
    public function index()
    {
        $configurations = SystemConfiguration::all()->mapWithKeys(function ($config) {
            return [$config->key => [
                'value' => $config->value,
                'type' => $config->type,
                'description' => $config->description,
            ]];
        });

        return response()->json([
            'status' => 'success',
            'data' => $configurations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update system configurations
     */
    public function update(Request $request, string $id = null)
    {
        $validator = Validator::make($request->all(), [
            'configurations' => 'required|array',
            'configurations.*.key' => 'required|string',
            'configurations.*.value' => 'nullable',
            'configurations.*.type' => 'nullable|string|in:string,integer,boolean,json,file',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->configurations as $config) {
            $type = $config['type'] ?? 'string';
            SystemConfiguration::set(
                $config['key'],
                $config['value'],
                $type,
                $config['description'] ?? null
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Configurations updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Upload logo
     */
    public function uploadLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete old logo if exists
        $oldLogo = SystemConfiguration::get('app_logo');
        if ($oldLogo && Storage::exists($oldLogo)) {
            Storage::delete($oldLogo);
        }

        // Store new logo
        $path = $request->file('logo')->store('public/logos');
        
        // Save to configuration
        SystemConfiguration::set('app_logo', $path, 'file', 'Application logo');

        return response()->json([
            'status' => 'success',
            'message' => 'Logo uploaded successfully',
            'data' => [
                'path' => $path,
                'url' => Storage::url($path)
            ]
        ]);
    }

    /**
     * Get logo URL
     */
    public function getLogo()
    {
        $logoPath = SystemConfiguration::get('app_logo');
        
        if (!$logoPath) {
            return response()->json([
                'status' => 'success',
                'data' => null
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'path' => $logoPath,
                'url' => Storage::url($logoPath)
            ]
        ]);
    }
}
