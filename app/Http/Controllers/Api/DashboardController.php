<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanDetail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get user dashboard data
     */
    public function appDashboard(Request $request)
    {
        $userId = $request->user()->id;
        
        // Get the latest loan for the user
        $latestLoan = LoanDetail::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestLoan) {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'no_loans',
                    'message' => 'No loan applications found'
                ]
            ]);
        }

        // If latest loan is pending
        if ($latestLoan->status === 'pending') {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'pending',
                    'message' => 'Application under review',
                    'application_submitted_date' => $latestLoan->created_at->format('Y-m-d H:i:s')
                ]
            ]);
        }
        // If latest loan is pending
        if ($latestLoan->status === 'completed') {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'completed',
                    'message' => 'Loan has been paid off',
                    'application_submitted_date' => $latestLoan->created_at->format('Y-m-d H:i:s')
                ]
            ]);
        }

        // If latest loan is approved/running
        if ($latestLoan->status === 'approved') {
            // Get next unpaid transaction
            $nextTransaction = Transaction::where('loan_id', $latestLoan->loan_id)
                ->where('status', '!=', 'completed')
                ->orderBy('due_date', 'asc')
                ->first();

            // Calculate outstanding amount
            $outstandingTransactions = Transaction::where('loan_id', $latestLoan->loan_id)
                ->where('status', '!=', 'completed')
                ->get();

            $outstandingAmount = $outstandingTransactions->sum(function($transaction) {
                return $transaction->amount + $transaction->late_fee;
            });

            // Update transaction statuses based on current date
            foreach ($outstandingTransactions as $transaction) {
                $transaction->updateStatus();
            }

            $response = [
                'success' => true,
                'data' => [
                    'status' => 'approved',
                    'loanid' => $latestLoan->loan_id,
                    'outstanding' => round($outstandingAmount, 2)
                ]
            ];

            if ($nextTransaction) {
                $response['data']['nextEmi'] = round($nextTransaction->amount + $nextTransaction->late_fee, 2);
                $response['data']['due_on'] = $nextTransaction->due_date->format('Y-m-d');
            } else {
                $response['data']['nextEmi'] = 0;
                $response['data']['due_on'] = null;
                $response['data']['message'] = 'All payments completed';
            }

            return response()->json($response);
        }

        // If loan is rejected
        return response()->json([
            'success' => true,
            'data' => [
                'status' => 'rejected',
                'message' => 'Loan application was rejected'
            ]
        ]);
    }
}

