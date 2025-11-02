<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    /**
     * Submit KYC application
     */
    public function apply(Request $request)
    {
        // Check if already submitted
        if ($request->user()->hasSubmittedKyc()) {
            return response()->json([
                'success' => false,
                'message' => 'KYC application already submitted'
            ], 400);
        }

        $validated = $request->validate([
            // Personal Details
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'nullable|in:Male,Female,Other',
            'nationality' => 'required|string|max:255',
            
            // Contact
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email',
            'alternate_contact' => 'nullable|string|max:20',
            
            // Address
            'current_address' => 'required|string',
            'current_city' => 'required|string|max:255',
            'current_state' => 'required|string|max:255',
            'current_zip_code' => 'required|string|max:10',
            'residential_status' => 'required|in:Own,Rent,Family,Other',
            'years_at_current_address' => 'required|integer|min:0',
            'address_same_as_current' => 'boolean',
            'permanent_address' => 'required_if:address_same_as_current,false|nullable|string',
            'permanent_city' => 'required_if:address_same_as_current,false|nullable|string|max:255',
            'permanent_state' => 'required_if:address_same_as_current,false|nullable|string|max:255',
            'permanent_zip_code' => 'required_if:address_same_as_current,false|nullable|string|max:10',
            
            // Employment
            'employment_type' => 'required|in:Salaried,Self-employed,Student,Retired,Other',
            'employer_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
            'other_income' => 'nullable|numeric|min:0',
            'employment_tenure_months' => 'required|integer|min:0',
            
            // Loan
            'loan_amount' => 'required|numeric|min:1000',
            'loan_tenure_months' => 'required|integer|min:1|max:360',
            'loan_purpose' => 'required|string',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            
            // KYC
            'aadhar_number' => 'required|string|size:12',
            'pan_number' => 'required|string|size:10',
            'photograph' => 'nullable|image|max:2048',
            'address_proof' => 'nullable|file|max:2048',
            'aadhar_card' => 'nullable|file|max:2048',
            'pan_card' => 'nullable|file|max:2048',
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

        // Handle permanent address
        if ($request->boolean('address_same_as_current')) {
            $validated['permanent_address'] = $validated['current_address'];
            $validated['permanent_city'] = $validated['current_city'];
            $validated['permanent_state'] = $validated['current_state'];
            $validated['permanent_zip_code'] = $validated['current_zip_code'];
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

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

        $application = KycApplication::create($validated);

        // Update user KYC status
        $request->user()->update([
            'kyc_status' => 'pending',
            'from_register' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KYC Application submitted successfully!',
            'data' => [
                'application' => $application,
                'user_kyc_status' => 'pending'
            ]
        ], 201);
    }

    /**
     * Get KYC application status
     */
    public function status(Request $request)
    {
        $application = $request->user()->kycApplication;
        
        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'No KYC application found. Please submit your application.',
                'data' => [
                    'user_kyc_status' => 'not_submitted',
                    'can_access_dashboard' => false
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'application' => [
                    'id' => $application->id,
                    'user_id' => $application->user_id,
                    'full_name' => $application->full_name,
                    'status' => $application->status,
                    'loan_amount' => $application->loan_amount,
                    'loan_tenure_months' => $application->loan_tenure_months,
                    'estimated_emi' => $application->estimated_emi,
                    'submitted_at' => $application->created_at,
                    'reviewed_at' => $application->reviewed_at,
                    'admin_notes' => $application->admin_notes,
                ],
                'user_kyc_status' => $request->user()->kyc_status,
                'can_access_dashboard' => $request->user()->isKycApproved()
            ]
        ]);
    }

    /**
     * Get full KYC application details
     */
    public function myApplication(Request $request)
    {
        $application = $request->user()->kycApplication;
        
        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'No KYC application found'
            ], 404);
        }

        // Prepare document URLs
        $documents = [
            'photograph' => $application->photograph_path ? url('storage/' . $application->photograph_path) : null,
            'address_proof' => $application->address_proof_path ? url('storage/' . $application->address_proof_path) : null,
            'aadhar_card' => $application->aadhar_card_path ? url('storage/' . $application->aadhar_card_path) : null,
            'pan_card' => $application->pan_card_path ? url('storage/' . $application->pan_card_path) : null,
        ];

        $data = $application->toArray();
        $data['documents'] = $documents;
        $data['age'] = $application->date_of_birth->diffInYears(now());

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}

