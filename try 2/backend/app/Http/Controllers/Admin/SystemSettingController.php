<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SystemSettingController extends Controller
{
    /**
     * Get all system settings
     */
    public function index()
    {
        $settings = [
            'system_name' => SystemSetting::get('system_name', 'Request Workflow System'),
            'registration_mode' => SystemSetting::get('registration_mode', 'public'),
            'items_per_page' => SystemSetting::get('items_per_page', '15'),
            'date_format' => SystemSetting::get('date_format', 'Y-m-d'),
            'timezone' => SystemSetting::get('timezone', 'UTC'),
            'enable_notifications' => SystemSetting::get('enable_notifications', 'true'),
        ];

        return response()->json($settings);
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'system_name'          => 'nullable|string|max:255',
            'registration_mode'    => 'nullable|in:public,admin_only',
            'items_per_page'       => 'nullable|in:10,15,25,50,100',
            'date_format'          => 'nullable|string|max:50',
            'timezone'             => 'nullable|string|max:100',
            'enable_notifications' => 'nullable|in:true,false,1,0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update only known settings
        $allowed = ['system_name', 'registration_mode', 'items_per_page', 'date_format', 'timezone', 'enable_notifications'];
        foreach ($allowed as $key) {
            if ($request->has($key) && $request->input($key) !== null) {
                SystemSetting::set($key, (string) $request->input($key));
            }
        }

        return response()->json([
            'message' => 'Settings updated successfully',
            'settings' => [
                'system_name' => SystemSetting::get('system_name', 'Request Workflow System'),
                'registration_mode' => SystemSetting::get('registration_mode', 'public'),
                'items_per_page' => SystemSetting::get('items_per_page', '15'),
                'date_format' => SystemSetting::get('date_format', 'Y-m-d'),
                'timezone' => SystemSetting::get('timezone', 'UTC'),
                'enable_notifications' => SystemSetting::get('enable_notifications', 'true'),
            ]
        ]);
    }

    /**
     * Reset settings to default
     */
    public function reset()
    {
        SystemSetting::set('system_name', 'Request Workflow System');
        SystemSetting::set('registration_mode', 'public');
        SystemSetting::set('items_per_page', '15');
        SystemSetting::set('date_format', 'Y-m-d');
        SystemSetting::set('timezone', 'UTC');
        SystemSetting::set('enable_notifications', 'true');

        return response()->json([
            'message' => 'Settings reset to default successfully',
            'settings' => [
                'system_name' => 'Request Workflow System',
                'registration_mode' => 'public',
                'items_per_page' => '15',
                'date_format' => 'Y-m-d',
                'timezone' => 'UTC',
                'enable_notifications' => 'true',
            ]
        ]);
    }
}

