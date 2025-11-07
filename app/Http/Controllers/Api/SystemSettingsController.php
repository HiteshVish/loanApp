<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    /**
     * Get all system settings as key-value pairs
     */
    public function index()
    {
        $settings = SystemSetting::select('key', 'value')->get();
        
        $keyValuePairs = [];
        foreach ($settings as $setting) {
            $keyValuePairs[$setting->key] = $setting->value;
        }
        
        return response()->json([
            'success' => true,
            'data' => $keyValuePairs
        ]);
    }
}

