<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycApplication;
use App\Models\User;
use App\Models\LoanDetail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics
     */
    public function index()
    {
        // Get user stats
        $totalUsers = User::count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $unverifiedUsers = User::whereNull('email_verified_at')->count();
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Get KYC stats
        $totalApplications = KycApplication::count();
        $pendingApplications = KycApplication::where('status', 'pending')->count();
        $approvedApplications = KycApplication::where('status', 'approved')->count();
        $rejectedApplications = KycApplication::where('status', 'rejected')->count();
        $newApplicationsThisMonth = KycApplication::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Get recent loans (last 5) - more relevant than KYC applications
        $recentLoans = LoanDetail::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Get loans and collections by month for chart (last 6 months)
        $loansChartData = [];
        $collectionsChartData = [];
        $months = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            // Count loans created in this month
            $loansChartData[] = LoanDetail::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            
            // Sum collections (paid_amount) for transactions completed in this month
            $collectionsChartData[] = Transaction::where('status', 'completed')
                ->whereMonth('paid_date', $date->month)
                ->whereYear('paid_date', $date->year)
                ->sum('paid_amount');
        }

        // Get status distribution for pie chart (using Loan Status instead of KYC Application Status)
        $statusDistribution = [
            'Pending' => $pendingLoans,
            'Approved' => $approvedLoans,
            'Completed' => $completedLoans,
            'Rejected' => $rejectedLoans
        ];

        // Calculate growth percentages
        $lastMonthUsers = User::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        $userGrowth = $lastMonthUsers > 0 
            ? round((($newUsersThisMonth - $lastMonthUsers) / $lastMonthUsers) * 100, 1) 
            : 0;

        $lastMonthApplications = KycApplication::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        $applicationGrowth = $lastMonthApplications > 0 
            ? round((($newApplicationsThisMonth - $lastMonthApplications) / $lastMonthApplications) * 100, 1) 
            : 0;

        // Get loan statistics
        $totalLoans = LoanDetail::count();
        $pendingLoans = LoanDetail::where('status', 'pending')->count();
        $approvedLoans = LoanDetail::where('status', 'approved')->count();
        $completedLoans = LoanDetail::where('status', 'completed')->count();
        $rejectedLoans = LoanDetail::where('status', 'rejected')->count();
        
        // Get total loan amounts
        $totalLoanAmount = LoanDetail::sum('loan_amount');
        $approvedLoanAmount = LoanDetail::where('status', 'approved')->sum('loan_amount');
        $pendingLoanAmount = LoanDetail::where('status', 'pending')->sum('loan_amount');
        
        // Get collection statistics
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfDay = Carbon::today()->startOfDay();
        
        // Daily collection - use paid_amount (actual amount collected)
        $dailyCollection = Transaction::where('status', 'completed')
            ->whereDate('paid_date', $today)
            ->sum('paid_amount');
        
        // Monthly collection - use paid_amount (actual amount collected)
        $monthlyCollection = Transaction::where('status', 'completed')
            ->whereDate('paid_date', '>=', $startOfMonth)
            ->sum('paid_amount');
        
        // Total collection (all time) - use paid_amount (actual amount collected)
        $totalCollection = Transaction::where('status', 'completed')
            ->sum('paid_amount');
        
        // Late fees collected
        $dailyLateFees = Transaction::where('status', 'completed')
            ->whereDate('paid_date', $today)
            ->sum('late_fee');
        
        $monthlyLateFees = Transaction::where('status', 'completed')
            ->whereDate('paid_date', '>=', $startOfMonth)
            ->sum('late_fee');
        
        // Pending payments (overdue) - includes both pending and delayed transactions
        // Pending: transactions that are overdue but not yet marked as delayed
        // Delayed: transactions that are overdue and have been marked as delayed
        $overduePending = Transaction::where('status', 'pending')
            ->where('due_date', '<', $today)
            ->count();
        
        $overdueDelayed = Transaction::where('status', 'delayed')
            ->count();
        
        // Total pending payments (overdue pending + delayed)
        $pendingPayments = $overduePending + $overdueDelayed;
        
        // Calculate total pending payment amount (including late fees)
        // For pending overdue transactions
        $pendingPaymentAmount = Transaction::where('status', 'pending')
            ->where('due_date', '<', $today)
            ->get()
            ->sum(function($t) {
                return $t->amount + ($t->late_fee ?? 0);
            });
        
        // Add delayed payment amounts
        $pendingPaymentAmount += Transaction::where('status', 'delayed')
            ->get()
            ->sum(function($t) {
                return $t->amount + ($t->late_fee ?? 0);
            });
        
        // Delayed payments
        $delayedPayments = Transaction::where('status', 'delayed')
            ->count();
        
        $delayedPaymentAmount = Transaction::where('status', 'delayed')
            ->get()
            ->sum(function($t) {
                return $t->amount + $t->late_fee;
            });
        
        // New loans this month
        $newLoansThisMonth = LoanDetail::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return view('dashboard', compact(
            'totalUsers',
            'verifiedUsers',
            'unverifiedUsers',
            'newUsersThisMonth',
            'userGrowth',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'newApplicationsThisMonth',
            'applicationGrowth',
            'recentLoans',
            'loansChartData',
            'collectionsChartData',
            'months',
            'statusDistribution',
            'totalLoans',
            'pendingLoans',
            'approvedLoans',
            'completedLoans',
            'rejectedLoans',
            'totalLoanAmount',
            'approvedLoanAmount',
            'pendingLoanAmount',
            'dailyCollection',
            'monthlyCollection',
            'totalCollection',
            'dailyLateFees',
            'monthlyLateFees',
            'pendingPayments',
            'pendingPaymentAmount',
            'delayedPayments',
            'delayedPaymentAmount',
            'newLoansThisMonth'
        ));
    }
}

