<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    /**
     * Get a single system setting value by key
     */
    public function index(Request $request)
    {
        $key = $request->query('key');
        
        if (!$key) {
            return response()->json([
                'success' => false,
                'message' => 'Key parameter is required'
            ], 400);
        }
        
        $setting = SystemSetting::where('key', $key)->first();
        
        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'key' => $setting->key,
                'value' => $setting->value
            ]
        ]);
    }
}

