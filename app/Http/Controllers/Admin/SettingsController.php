<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Update the default interest rate
     */
    public function updateInterestRate(Request $request)
    {
        $request->validate([
            'interest_rate' => 'required|numeric|min:0|max:100',
            'rate_type' => 'nullable|string|in:fixed,variable,reducing',
        ]);

        // Save interest rate
        SystemSetting::set('default_interest_rate', $request->interest_rate, 'number');
        
        // Save rate type if provided
        if ($request->has('rate_type')) {
            SystemSetting::set('interest_rate_type', $request->rate_type, 'string');
        }

        return response()->json([
            'success' => true,
            'message' => 'Interest rate updated successfully',
            'rate' => $request->interest_rate,
        ]);
    }

    /**
     * Get current interest rate
     */
    public function getInterestRate()
    {
        $rate = SystemSetting::get('default_interest_rate', 10);
        $type = SystemSetting::get('interest_rate_type', 'fixed');

        return response()->json([
            'rate' => $rate,
            'type' => $type,
        ]);
    }
}

