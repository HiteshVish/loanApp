<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    /**
     * Get all users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by KYC status
        if ($request->has('kyc_status')) {
            $query->where('kyc_status', $request->kyc_status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $users = $query->latest()->paginate($perPage);

        $usersData = $users->getCollection()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'kyc_status' => $user->kyc_status,
                'from_register' => $user->from_register,
                'avatar' => $user->avatar,
                'has_kyc_application' => $user->kycApplication !== null,
                'created_at' => $user->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $usersData,
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get single user
     */
    public function show($id)
    {
        $user = User::with('kycApplication')->findOrFail($id);

        $data = [
            'user' => $user,
            'kyc_application' => $user->kycApplication ? [
                'id' => $user->kycApplication->id,
                'status' => $user->kycApplication->status,
                'loan_amount' => $user->kycApplication->loan_amount,
                'submitted_at' => $user->kycApplication->created_at,
            ] : null,
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => 'sometimes|in:user,admin',
            'kyc_status' => 'sometimes|in:not_submitted,pending,under_review,approved,rejected',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Don't allow deleting yourself
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();
        if ($user->id === $currentUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}

