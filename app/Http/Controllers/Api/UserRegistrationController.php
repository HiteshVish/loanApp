<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserRegistrationController extends Controller
{
    /**
     * Store user registration form data
     */
    public function form(Request $request)
    {
        $validated = $request->validate([
            // Personal Details
            'name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string|in:male,female,other',
            'nationality' => 'required|string|max:255',
            
            // Contact Info
            'mob' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'currentAdd' => 'required|string',
            'permanentAdd' => 'required|string',
            
            // Documents - Files
            'aadhar' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'pan' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'photo' => 'nullable|file|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Get user ID from authenticated user
        $userId = $request->user()->id;

        // Handle file uploads
        $aadharPath = null;
        $panPath = null;
        $photoPath = null;

        if ($request->hasFile('aadhar')) {
            $aadharFile = $request->file('aadhar');
            $aadharPath = $aadharFile->store('documents/aadhar', 'public');
        }

        if ($request->hasFile('pan')) {
            $panFile = $request->file('pan');
            $panPath = $panFile->store('documents/pan', 'public');
        }

        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');
            $photoPath = $photoFile->store('documents/photo', 'public');
        }

        // Check if user already has details
        $existingDetail = UserDetail::where('user_id', $userId)->first();
        
        if ($existingDetail) {
            // Delete old files if new ones are uploaded
            if ($aadharPath && $existingDetail->aadhar) {
                Storage::disk('public')->delete($existingDetail->aadhar);
            }
            if ($panPath && $existingDetail->pan) {
                Storage::disk('public')->delete($existingDetail->pan);
            }
            if ($photoPath && $existingDetail->photo) {
                Storage::disk('public')->delete($existingDetail->photo);
            }

            // Update existing details
            $updateData = [
                'name' => $validated['name'],
                'dob' => $validated['dob'],
                'gender' => $validated['gender'],
                'nationality' => $validated['nationality'],
                'mobile' => $validated['mob'],
                'email' => $validated['email'],
                'current_address' => $validated['currentAdd'],
                'permanent_address' => $validated['permanentAdd'],
            ];

            // Only update file paths if new files are uploaded
            if ($aadharPath) $updateData['aadhar'] = $aadharPath;
            if ($panPath) $updateData['pan'] = $panPath;
            if ($photoPath) $updateData['photo'] = $photoPath;

            $existingDetail->update($updateData);
            $userDetail = $existingDetail;
        } else {
            // Create new details
            $userDetail = UserDetail::create([
                'user_id' => $userId,
                'name' => $validated['name'],
                'dob' => $validated['dob'],
                'gender' => $validated['gender'],
                'nationality' => $validated['nationality'],
                'mobile' => $validated['mob'],
                'email' => $validated['email'],
                'current_address' => $validated['currentAdd'],
                'permanent_address' => $validated['permanentAdd'],
                'aadhar' => $aadharPath,
                'pan' => $panPath,
                'photo' => $photoPath,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User registration form submitted successfully'
        ], 200);
    }
}
