<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Store loan details and return loan ID
     */
    public function loanDetails(Request $request)
    {
        $validated = $request->validate([
            'loanAmount' => 'required|numeric|min:100|max:10000000', // Min 1K, Max 1Cr
            'tenure' => 'required|integer|min:1|max:60', // Min 1 month, Max 60 months
        ]);

        // Get user ID from authenticated user
        $userId = $request->user()->id;

        // Create new loan detail (transactions are auto-generated in model boot method)
        $loanDetail = LoanDetail::create([
            'user_id' => $userId,
            'loan_amount' => $validated['loanAmount'],
            'tenure' => $validated['tenure'],
            'status' => 'pending', // Auto-approve for now
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Loan details submitted successfully'
        ], 200);
    }

    /**
     * Get all loan applications for the authenticated user
     */
    public function loanApplications(Request $request)
    {
        $userId = $request->user()->id;
        
        // Get all loans for the user
        $loans = LoanDetail::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($loans->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No loan applications found',
                'data' => []
            ]);
        }

        // Format loan data
        $formattedLoans = $loans->map(function($loan) {
            $endDate = Carbon::parse($loan->created_at)->addMonths($loan->tenure);
            
            return [
                'loanid' => $loan->loan_id,
                'amount' => round($loan->loan_amount, 2),
                'enddate' => $endDate->format('Y-m-d'),
                'tenure' => $loan->tenure,
                'applied_date' => $loan->created_at->format('Y-m-d H:i:s'),
                'status' => $loan->status,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Loan applications retrieved successfully',
            'data' => $formattedLoans
        ]);
    }

    /**
     * Get loan application summary statistics for the authenticated user
     */
    public function loanApplicationSummary(Request $request)
    {
        $userId = $request->user()->id;
        
        // Get all loans for the user
        $loans = LoanDetail::where('user_id', $userId)->get();

        // Calculate statistics
        $totalApplications = $loans->count();
        $approvedApplications = $loans->where('status', 'approved')->count();
        $totalAmount = $loans->sum('loan_amount');
        $successRate = $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'message' => 'Loan application summary retrieved successfully',
            'data' => [
                'total_application' => $totalApplications,
                'approved' => $approvedApplications,
                'total_amount' => round($totalAmount, 2),
                'successrate' => $successRate
            ]
        ]);
    }
}
