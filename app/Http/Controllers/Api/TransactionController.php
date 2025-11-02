<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanDetail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Get transaction details for user
     */
    public function transaction(Request $request)
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
                    'application_submitted_date' => $latestLoan->created_at->format('Y-m-d H:i:s')
                ]
            ]);
        }

        // If latest loan is approved/running
        if ($latestLoan->status === 'approved') {
            // Get all transactions for this loan
            $transactions = Transaction::where('loan_id', $latestLoan->loan_id)
                ->orderBy('due_date', 'asc')
                ->get();

            // Update transaction statuses based on current date
            foreach ($transactions as $transaction) {
                $transaction->updateStatus();
            }

            // Format transactions for response
            $formattedTransactions = $transactions->map(function($transaction) {
                $statusText = $transaction->status;
                if ($transaction->status === 'delayed' && $transaction->paid_date) {
                    $statusText = 'delayed (paid)';
                } elseif ($transaction->status === 'delayed' && !$transaction->paid_date) {
                    $statusText = 'delayed (unpaid)';
                }

                return [
                    'amount' => round($transaction->amount + $transaction->late_fee, 2),
                    'date' => $transaction->due_date->format('d M Y'),
                    'status' => $statusText,
                    'days_late' => $transaction->days_late,
                    'late_fee' => round($transaction->late_fee, 2)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'approved',
                    'loanid' => $latestLoan->loan_id,
                    'transactions' => $formattedTransactions
                ]
            ]);
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
