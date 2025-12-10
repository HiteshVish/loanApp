<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserReferencePhone;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of all users (admin only)
     */
    public function index(Request $request)
    {
        return view('admin.users.index');
    }

    /**
     * Get users data for DataTables (server-side processing)
     */
    public function getUsersData(Request $request)
    {
        $query = User::query();
        
        // Get DataTables parameters
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $searchValue = $request->get('search')['value'] ?? '';
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        
        // Define column mapping
        $columns = ['id', 'name', 'email', 'role', 'created_at', 'email_verified_at'];
        $orderColumnName = $columns[$orderColumn] ?? 'created_at';
        
        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('id', 'like', "%{$searchValue}%");
            });
        }
        
        // Get total records before filtering
        $totalRecords = User::count();
        $filteredRecords = $query->count();
        
        // Apply ordering and pagination
        $users = $query->orderBy($orderColumnName, $orderDir)
                      ->skip($start)
                      ->take($length)
                      ->get();
        
        // Format data for DataTables
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at->format('M d, Y'),
                'email_verified_at' => $user->email_verified_at ? 'verified' : 'unverified',
                'avatar' => $user->avatar,
                'is_admin' => $user->isAdmin(),
                'is_current_user' => $user->id === auth()->id(),
            ];
        }
        
        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * Display user details with all loans
     */
    public function show(User $user)
    {
        // Load user with all relationships
        $user->load([
            'userDetail',
            'loanDetails' => function($query) {
                $query->with('transactions')->orderBy('created_at', 'desc');
            },
            'kycApplication'
        ]);
        
        // Fetch ALL contacts for this user by user_id
        $contacts = UserReferencePhone::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $user->setRelation('referencePhones', $contacts);
        
        // Fetch ALL locations for this user by user_id
        $allLocations = UserLocation::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $user->setRelation('locations', $allLocations);
        
        // Get latest 10 for display
        $user->latestLocations = $allLocations->take(10);

        // Calculate loan statistics for each loan
        foreach ($user->loanDetails as $loan) {
            $loan->processing_fee = $loan->calculateProcessingFee();
            $loan->in_hand_amount = $loan->calculateInHandAmount();
            $loan->total_amount_with_interest = $loan->calculateTotalAmount();
            $loan->daily_emi = $loan->calculateDailyEMI();
            $loan->late_fee_per_day = $loan->calculateLateFeePerDay();
            
            $loan->total_paid = $loan->transactions->where('status', 'completed')->sum('amount');
            $loan->remaining_amount = $loan->total_amount_with_interest - $loan->total_paid;
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user (admin only)
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user (admin only)
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:user,admin'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user (admin only)
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Verify user email (admin only)
     */
    public function verifyEmail(User $user)
    {
        if ($user->email_verified_at) {
            return redirect()->route('admin.users.index')->with('info', 'Email is already verified!');
        }

        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User email verified successfully!');
    }
}
