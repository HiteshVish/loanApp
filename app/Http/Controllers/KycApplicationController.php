<?php

namespace App\Http\Controllers;

use App\Models\KycApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycApplicationController extends Controller
{
    /**
     * Show KYC application form
     */
    public function create()
    {
        // Check if already submitted
        if (auth()->user()->hasSubmittedKyc()) {
            return redirect()->route('kyc.status');
        }

        return view('kyc.create');
    }

    /**
     * Store KYC application
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Personal Details
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'nullable|in:Male,Female,Other',
            'nationality' => 'required|string|max:255',
            
            // Contact
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email',
            'alternate_contact' => 'nullable|string|max:20', // Can be same as mobile_number
            
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
            'employer_name' => 'required_if:employment_type,Salaried,Self-employed|nullable|string|max:255',
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

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        KycApplication::create($validated);

        // Update user KYC status
        auth()->user()->update(['kyc_status' => 'pending']);

        return redirect()->route('kyc.status')->with('success', 'KYC Application submitted successfully!');
    }

    /**
     * Show KYC application status
     */
    public function status()
    {
        $application = auth()->user()->kycApplication;
        
        if (!$application) {
            return redirect()->route('kyc.create');
        }

        return view('kyc.status', compact('application'));
    }
}
