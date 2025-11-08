<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Store new location for authenticated user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // Get user ID from authenticated user
        $userId = $request->user()->id;

        // Create new location
        $location = UserLocation::create([
            'user_id' => $userId,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location saved successfully'
        ], 201);
    }
}
