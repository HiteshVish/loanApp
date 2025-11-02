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
            'personalDetail' => 'required|array',
            'personalDetail.name' => 'required|string|max:255',
            'personalDetail.dob' => 'required|date',
            'personalDetail.gender' => 'required|string|in:male,female,other',
            'personalDetail.nationality' => 'required|string|max:255',
            
            // Contact Info
            'contactInfo' => 'required|array',
            'contactInfo.mob' => 'required|string|max:20',
            'contactInfo.email' => 'required|email|max:255',
            'contactInfo.currentAdd' => 'required|string',
            'contactInfo.permanentAdd' => 'required|string',
            
            // Documents - Now as files
            'document' => 'required|array',
            'document.aadhar' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'document.pan' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'document.photo' => 'nullable|file|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Get user ID from authenticated user
        $userId = $request->user()->id;

        // Handle file uploads
        $aadharPath = null;
        $panPath = null;
        $photoPath = null;

        if ($request->hasFile('document.aadhar')) {
            $aadharFile = $request->file('document.aadhar');
            $aadharPath = $aadharFile->store('documents/aadhar', 'public');
        }

        if ($request->hasFile('document.pan')) {
            $panFile = $request->file('document.pan');
            $panPath = $panFile->store('documents/pan', 'public');
        }

        if ($request->hasFile('document.photo')) {
            $photoFile = $request->file('document.photo');
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
                'name' => $validated['personalDetail']['name'],
                'dob' => $validated['personalDetail']['dob'],
                'gender' => $validated['personalDetail']['gender'],
                'nationality' => $validated['personalDetail']['nationality'],
                'mobile' => $validated['contactInfo']['mob'],
                'email' => $validated['contactInfo']['email'],
                'current_address' => $validated['contactInfo']['currentAdd'],
                'permanent_address' => $validated['contactInfo']['permanentAdd'],
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
                'name' => $validated['personalDetail']['name'],
                'dob' => $validated['personalDetail']['dob'],
                'gender' => $validated['personalDetail']['gender'],
                'nationality' => $validated['personalDetail']['nationality'],
                'mobile' => $validated['contactInfo']['mob'],
                'email' => $validated['contactInfo']['email'],
                'current_address' => $validated['contactInfo']['currentAdd'],
                'permanent_address' => $validated['contactInfo']['permanentAdd'],
                'aadhar' => $aadharPath,
                'pan' => $panPath,
                'photo' => $photoPath,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User registration form submitted successfully',
            'data' => [
                'user_id' => $userId,
                'user_detail' => $userDetail,
                'uploaded_files' => [
                    'aadhar' => $aadharPath,
                    'pan' => $panPath,
                    'photo' => $photoPath,
                ]
            ]
        ], 200);
    }
}
