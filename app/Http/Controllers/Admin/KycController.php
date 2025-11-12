<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycApplication;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\LoanDetail;
use App\Models\UserReferencePhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    /**
     * Display all KYC applications (each loan is a separate application)
     */
    public function index(Request $request)
    {
        // Query loans - each loan is a separate application
        $query = \App\Models\LoanDetail::with(['user.userDetail', 'user']);
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Search by name, email, Loan ID, or user details
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('loan_id', 'like', "%{$search}%")
                  ->orWhere('loan_amount', 'like', "%{$search}%")
                  ->orWhereHas('user', function($query) use ($search) {
                      $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user.userDetail', function($query) use ($search) {
                      $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%")
                            ->orWhere('aadhar', 'like', "%{$search}%")
                            ->orWhere('pan', 'like', "%{$search}%");
                  });
            });
        }
        
        $loans = $query->latest()->paginate(20)->withQueryString();
        
        // Calculate stats
        $stats = [
            'total' => \App\Models\LoanDetail::count(),
            'pending' => \App\Models\LoanDetail::where('status', 'pending')->count(),
            'approved' => \App\Models\LoanDetail::where('status', 'approved')->count(),
            'rejected' => \App\Models\LoanDetail::where('status', 'rejected')->count(),
        ];
            
        return view('admin.kyc.index', compact('loans', 'stats'));
    }

    /**
     * Show single KYC application details (for a specific loan)
     */
    public function show(\App\Models\LoanDetail $loan)
    {
        $loan->load(['user.userDetail', 'user', 'user.referencePhones', 'user.locations']);
        return view('admin.kyc.show', ['loan' => $loan]);
    }

    /**
     * Show all contacts for KYC application
     */
    public function contacts(\App\Models\LoanDetail $loan)
    {
        $loan->load(['user', 'user.referencePhones']);
        return view('admin.kyc.contacts', ['loan' => $loan]);
    }

    /**
     * Delete a contact from KYC application
     */
    public function deleteContact(\App\Models\LoanDetail $loan, UserReferencePhone $contact)
    {
        // Verify that the contact belongs to the loan's user
        if ($contact->user_id !== $loan->user_id) {
            return redirect()->route('admin.kyc.contacts', $loan)
                ->with('error', 'Contact not found or does not belong to this application.');
        }

        $contactNumber = $contact->contact_number;
        $contact->delete();

        return redirect()->route('admin.kyc.contacts', $loan)
            ->with('success', 'Contact ' . $contactNumber . ' deleted successfully!');
    }

    /**
     * Show all locations for KYC application
     */
    public function locations(\App\Models\LoanDetail $loan)
    {
        $loan->load(['user', 'user.locations']);
        return view('admin.kyc.locations', ['loan' => $loan]);
    }

    /**
     * Approve KYC application (approve a specific loan)
     */
    public function approve(Request $request, \App\Models\LoanDetail $loan)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        // Update loan status
        $loan->update(['status' => 'approved']);

        return redirect()->route('admin.kyc.index')
            ->with('success', 'Loan ' . $loan->loan_id . ' approved successfully!');
    }

    /**
     * Reject KYC application (reject a specific loan)
     */
    public function reject(Request $request, \App\Models\LoanDetail $loan)
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        // Update loan status
        $loan->update(['status' => 'rejected']);

        return redirect()->route('admin.kyc.index')
            ->with('success', 'Loan ' . $loan->loan_id . ' rejected.');
    }

    /**
     * Disapprove (unapprove) KYC application (change loan status)
     */
    public function disapprove(Request $request, \App\Models\LoanDetail $loan)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
            'new_status' => 'required|in:pending,approved,rejected',
        ]);

        $oldStatus = $loan->status;
        $loan->update(['status' => $request->new_status]);
        
        $message = 'Loan ' . $loan->loan_id . ' status changed from ' . ucfirst($oldStatus) . ' to ' . ucfirst($request->new_status);

        return redirect()->route('admin.kyc.show', $loan)
            ->with('success', $message);
    }

    /**
     * Show form to create new KYC application
     */
    public function create()
    {
        return view('admin.kyc.create');
    }

    /**
     * Store manually created KYC application
     */
    public function store(Request $request)
    {
        // Handle user creation if needed
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
                'from_register' => 0,
            ]);
            
            $userId = $newUser->id;
        }

        // Validate KYC fields
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
            'employment_tenure_months' => 'nullable|integer|min:0',
            'loan_amount' => 'required|numeric|min:1000',
            'loan_tenure_months' => 'required|integer|min:1|max:360',
            'loan_purpose' => 'required|string',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'aadhar_number' => 'required|string|size:12',
            'pan_number' => 'required|string|size:10',
            'photograph' => 'nullable|image|max:2048',
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
        $validated['reviewed_by'] = auth()->id();
        $validated['reviewed_at'] = now();

        // Create KYC application
        $application = KycApplication::create($validated);

        // Also create UserDetail for backward compatibility
        if ($userId) {
            UserDetail::updateOrCreate(
                ['user_id' => $userId],
                [
                    'name' => $validated['full_name'],
                    'dob' => $validated['date_of_birth'],
                    'gender' => $validated['gender'],
                    'nationality' => $validated['nationality'],
                    'mobile' => $validated['mobile_number'],
                    'email' => $validated['email'],
                    'current_address' => $validated['current_address'],
                    'permanent_address' => $validated['permanent_address'] ?? $validated['current_address'],
                    'aadhar' => $validated['aadhar_number'],
                    'pan' => $validated['pan_number'],
                    'photo' => $validated['photograph_path'] ?? null,
                ]
            );

            // Create LoanDetail
            LoanDetail::create([
                'user_id' => $userId,
                'loan_id' => $application->loan_id,
                'loan_amount' => $validated['loan_amount'],
                'tenure' => $validated['loan_tenure_months'],
                'status' => $validated['status'],
            ]);

            // Update user KYC status
            User::find($userId)->update([
                'kyc_status' => $validated['status'],
                'from_register' => 1,
            ]);
        }

        return redirect()->route('admin.kyc.index')
            ->with('success', 'KYC Application created successfully!');
    }

    /**
     * Show edit form for KYC application
     * @deprecated This method is no longer used in the new structure
     */
    public function edit(\App\Models\LoanDetail $loan)
    {
        return redirect()->route('admin.kyc.show', $loan);
    }

    /**
     * Update KYC application
     * @deprecated This method is no longer used in the new structure
     */
    public function update(Request $request, \App\Models\LoanDetail $loan)
    {
        return redirect()->route('admin.kyc.show', $loan)
            ->with('info', 'This function is no longer available.');
    }

    /**
     * Delete KYC application (delete a loan)
     */
    public function destroy(\App\Models\LoanDetail $loan)
    {
        $loanId = $loan->loan_id;
        $loan->delete();

        return redirect()->route('admin.kyc.index')
            ->with('success', 'Loan ' . $loanId . ' deleted successfully!');
    }
}
