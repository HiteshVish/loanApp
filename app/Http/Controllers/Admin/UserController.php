<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        $query = User::query();
        
        // Search by name, email, or ID
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
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
            'kycApplication',
            'locations',
            'referencePhones'
        ]);

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
