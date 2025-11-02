<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KycApiController extends Controller
{
    /**
     * Get all KYC applications
     */
    public function index(Request $request)
    {
        $query = KycApplication::with('user');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($query) use ($search) {
                      $query->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 20);
        $applications = $query->latest()->paginate($perPage);

        $stats = [
            'total' => KycApplication::count(),
            'pending' => KycApplication::where('status', 'pending')->count(),
            'under_review' => KycApplication::where('status', 'under_review')->count(),
            'approved' => KycApplication::where('status', 'approved')->count(),
            'rejected' => KycApplication::where('status', 'rejected')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'applications' => $applications->items(),
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $applications->currentPage(),
                    'per_page' => $applications->perPage(),
                    'total' => $applications->total(),
                    'last_page' => $applications->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get single KYC application
     */
    public function show($id)
    {
        $application = KycApplication::with('user', 'reviewer')->findOrFail($id);

        $data = $application->toArray();
        $data['age'] = $application->date_of_birth->diffInYears(now());
        $data['documents'] = [
            'photograph' => $application->photograph_path ? url('storage/' . $application->photograph_path) : null,
            'address_proof' => $application->address_proof_path ? url('storage/' . $application->address_proof_path) : null,
            'aadhar_card' => $application->aadhar_card_path ? url('storage/' . $application->aadhar_card_path) : null,
            'pan_card' => $application->pan_card_path ? url('storage/' . $application->pan_card_path) : null,
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Approve KYC application
     */
    public function approve(Request $request, $id)
    {
        $application = KycApplication::findOrFail($id);

        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $application->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $application->user->update([
            'kyc_status' => 'approved',
            'from_register' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KYC Application approved successfully!',
            'data' => [
                'id' => $application->id,
                'status' => 'approved',
                'admin_notes' => $application->admin_notes,
                'reviewed_by' => $application->reviewed_by,
                'reviewed_at' => $application->reviewed_at,
                'user_kyc_status' => 'approved'
            ]
        ]);
    }

    /**
     * Reject KYC application
     */
    public function reject(Request $request, $id)
    {
        $application = KycApplication::findOrFail($id);

        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $application->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $application->user->update([
            'kyc_status' => 'rejected',
            'from_register' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KYC Application rejected',
            'data' => [
                'id' => $application->id,
                'status' => 'rejected',
                'admin_notes' => $application->admin_notes,
                'reviewed_by' => $application->reviewed_by,
                'reviewed_at' => $application->reviewed_at,
                'user_kyc_status' => 'rejected'
            ]
        ]);
    }

    /**
     * Change KYC status (disapprove)
     */
    public function disapprove(Request $request, $id)
    {
        $application = KycApplication::findOrFail($id);

        $request->validate([
            'new_status' => 'required|in:pending,under_review,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $oldStatus = $application->status;

        $application->update([
            'status' => $request->new_status,
            'admin_notes' => $request->admin_notes ?? "Status changed from {$oldStatus} to {$request->new_status}",
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $application->user->update([
            'kyc_status' => $request->new_status,
            'from_register' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => "KYC Application status changed from " . ucfirst($oldStatus) . " to " . ucfirst($request->new_status),
            'data' => [
                'id' => $application->id,
                'old_status' => $oldStatus,
                'new_status' => $request->new_status,
                'admin_notes' => $application->admin_notes,
                'reviewed_by' => $application->reviewed_by,
                'reviewed_at' => $application->reviewed_at,
                'user_kyc_status' => $request->new_status
            ]
        ]);
    }

    /**
     * Create KYC application (admin creates for user)
     */
    public function store(Request $request)
    {
        // Check creation type
        $userId = null;
        
        if ($request->creation_type === 'create_user' || $request->creation_type === 'direct_fill') {
            // Validate user fields
            $userField = $request->creation_type === 'create_user' ? 'new_user' : 'direct_user';
            
            $userValidated = $request->validate([
                "{$userField}_name" => 'required|string|max:255',
                "{$userField}_email" => 'required|email|unique:users,email',
                "{$userField}_password" => 'required|string|min:8|confirmed',
            ]);
            
            // Create new user
            $newUser = User::create([
                'name' => $userValidated["{$userField}_name"],
                'email' => $userValidated["{$userField}_email"],
                'password' => Hash::make($userValidated["{$userField}_password"]),
                'role' => 'user',
                'kyc_status' => 'not_submitted',
                'from_register' => 0, // Admin created user, not from register
            ]);
            
            $userId = $newUser->id;
        }

        // Validate KYC fields (same as apply method)
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'nullable|in:Male,Female,Other',
            'nationality' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email',
            'alternate_contact' => 'nullable|string|max:20',
            'current_address' => 'required|string',
            'current_city' => 'required|string|max:255',
            'current_state' => 'required|string|max:255',
            'current_zip_code' => 'required|string|max:10',
            'residential_status' => 'required|in:Own,Rent,Family,Other',
            'years_at_current_address' => 'required|integer|min:0',
            'permanent_address' => 'nullable|string',
            'permanent_city' => 'nullable|string|max:255',
            'permanent_state' => 'nullable|string|max:255',
            'permanent_zip_code' => 'nullable|string|max:10',
            'employment_type' => 'required|in:Salaried,Self-employed,Student,Retired,Other',
            'employer_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
            'other_income' => 'nullable|numeric|min:0',
            'employment_tenure_months' => 'required|integer|min:0',
            'loan_amount' => 'required|numeric|min:1000',
            'loan_tenure_months' => 'required|integer|min:1|max:360',
            'loan_purpose' => 'required|string',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'aadhar_number' => 'required|string|size:12',
            'pan_number' => 'required|string|size:10',
            'photograph' => 'nullable|image|max:2048',
            'address_proof' => 'nullable|file|max:2048',
            'aadhar_card' => 'nullable|file|max:2048',
            'pan_card' => 'nullable|file|max:2048',
            'status' => 'required|in:pending,under_review,approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        // Handle file uploads
        if ($request->hasFile('photograph')) {
            $validated['photograph_path'] = $request->file('photograph')->store('kyc/photographs', 'public');
        }
        if ($request->hasFile('address_proof')) {
            $validated['address_proof_path'] = $request->file('address_proof')->store('kyc/address_proofs', 'public');
        }
        if ($request->hasFile('aadhar_card')) {
            $validated['aadhar_card_path'] = $request->file('aadhar_card')->store('kyc/aadhar', 'public');
        }
        if ($request->hasFile('pan_card')) {
            $validated['pan_card_path'] = $request->file('pan_card')->store('kyc/pan', 'public');
        }

        // Calculate EMI
        $P = $validated['loan_amount'];
        $r = ($validated['interest_rate'] ?? 10) / 12 / 100;
        $n = $validated['loan_tenure_months'];
        
        if ($r == 0) {
            $emi = $P / $n;
        } else {
            $emi = $P * $r * pow(1 + $r, $n) / (pow(1 + $r, $n) - 1);
        }
        $validated['estimated_emi'] = round($emi, 2);
        $validated['interest_rate'] = $validated['interest_rate'] ?? 10;
        $validated['user_id'] = $userId;
        $validated['reviewed_by'] = $request->user()->id;
        $validated['reviewed_at'] = now();

        $application = KycApplication::create($validated);

        // Update user KYC status
        $application->user->update([
            'kyc_status' => $validated['status'],
            'from_register' => 1 // Admin created KYC, mark as from register
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KYC Application created successfully!',
            'data' => [
                'user' => User::find($userId),
                'application' => $application
            ]
        ], 201);
    }

    /**
     * Update KYC application
     */
    public function update(Request $request, $id)
    {
        $application = KycApplication::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'nullable|in:Male,Female,Other',
            'nationality' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email',
            'alternate_contact' => 'nullable|string|max:20',
            'current_address' => 'required|string',
            'current_city' => 'required|string|max:255',
            'current_state' => 'required|string|max:255',
            'current_zip_code' => 'required|string|max:10',
            'residential_status' => 'required|in:Own,Rent,Family,Other',
            'years_at_current_address' => 'required|integer|min:0',
            'permanent_address' => 'nullable|string',
            'permanent_city' => 'nullable|string|max:255',
            'permanent_state' => 'nullable|string|max:255',
            'permanent_zip_code' => 'nullable|string|max:10',
            'employment_type' => 'required|in:Salaried,Self-employed,Student,Retired,Other',
            'employer_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
            'other_income' => 'nullable|numeric|min:0',
            'employment_tenure_months' => 'required|integer|min:0',
            'loan_amount' => 'required|numeric|min:1000',
            'loan_tenure_months' => 'required|integer|min:1|max:360',
            'loan_purpose' => 'required|string',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'aadhar_number' => 'required|string|size:12',
            'pan_number' => 'required|string|size:10',
            'photograph' => 'nullable|image|max:2048',
            'address_proof' => 'nullable|file|max:2048',
            'aadhar_card' => 'nullable|file|max:2048',
            'pan_card' => 'nullable|file|max:2048',
            'status' => 'required|in:pending,under_review,approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        // Handle file uploads
        if ($request->hasFile('photograph')) {
            if ($application->photograph_path) {
                Storage::disk('public')->delete($application->photograph_path);
            }
            $validated['photograph_path'] = $request->file('photograph')->store('kyc/photographs', 'public');
        }
        if ($request->hasFile('address_proof')) {
            if ($application->address_proof_path) {
                Storage::disk('public')->delete($application->address_proof_path);
            }
            $validated['address_proof_path'] = $request->file('address_proof')->store('kyc/address_proofs', 'public');
        }
        if ($request->hasFile('aadhar_card')) {
            if ($application->aadhar_card_path) {
                Storage::disk('public')->delete($application->aadhar_card_path);
            }
            $validated['aadhar_card_path'] = $request->file('aadhar_card')->store('kyc/aadhar', 'public');
        }
        if ($request->hasFile('pan_card')) {
            if ($application->pan_card_path) {
                Storage::disk('public')->delete($application->pan_card_path);
            }
            $validated['pan_card_path'] = $request->file('pan_card')->store('kyc/pan', 'public');
        }

        // Recalculate EMI
        $P = $validated['loan_amount'];
        $r = ($validated['interest_rate'] ?? 10) / 12 / 100;
        $n = $validated['loan_tenure_months'];
        
        if ($r == 0) {
            $emi = $P / $n;
        } else {
            $emi = $P * $r * pow(1 + $r, $n) / (pow(1 + $r, $n) - 1);
        }
        $validated['estimated_emi'] = round($emi, 2);
        $validated['interest_rate'] = $validated['interest_rate'] ?? 10;
        $validated['reviewed_by'] = $request->user()->id;
        $validated['reviewed_at'] = now();

        $application->update($validated);
        $application->user->update([
            'kyc_status' => $validated['status'],
            'from_register' => 1 // Admin updated KYC, mark as from register
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KYC Application updated successfully!',
            'data' => $application
        ]);
    }

    /**
     * Delete KYC application
     */
    public function destroy($id)
    {
        $application = KycApplication::findOrFail($id);

        // Delete files
        if ($application->photograph_path) {
            Storage::disk('public')->delete($application->photograph_path);
        }
        if ($application->address_proof_path) {
            Storage::disk('public')->delete($application->address_proof_path);
        }
        if ($application->aadhar_card_path) {
            Storage::disk('public')->delete($application->aadhar_card_path);
        }
        if ($application->pan_card_path) {
            Storage::disk('public')->delete($application->pan_card_path);
        }

        $userId = $application->user_id;
        $application->user->update([
            'kyc_status' => 'not_submitted',
            'from_register' => 0 // Reset flag when KYC is deleted
        ]);
        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'KYC Application deleted successfully',
            'data' => [
                'user_id' => $userId,
                'user_kyc_status' => 'not_submitted'
            ]
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function dashboard()
    {
        $kycStats = [
            'total' => KycApplication::count(),
            'pending' => KycApplication::where('status', 'pending')->count(),
            'under_review' => KycApplication::where('status', 'under_review')->count(),
            'approved' => KycApplication::where('status', 'approved')->count(),
            'rejected' => KycApplication::where('status', 'rejected')->count(),
        ];

        $userStats = [
            'total_users' => User::count(),
            'users_with_kyc' => User::whereHas('kycApplication')->count(),
            'users_without_kyc' => User::whereDoesntHave('kycApplication')->count(),
            'admin_users' => User::where('role', 'admin')->count(),
        ];

        $recentApplications = KycApplication::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($app) {
                return [
                    'id' => $app->id,
                    'user_name' => $app->user->name,
                    'status' => $app->status,
                    'loan_amount' => $app->loan_amount,
                    'submitted_at' => $app->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'kyc_stats' => $kycStats,
                'user_stats' => $userStats,
                'recent_applications' => $recentApplications,
            ]
        ]);
    }
}

