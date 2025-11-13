@extends('layouts.sneat')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('sneat/vendor/libs/apex-charts/apex-charts.css') }}" />
@endpush

@section('content')
<!-- Welcome Card -->
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="row g-0 align-items-center">
                <div class="col-sm-8">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Welcome back, {{ Auth::user()->name }}! 🎉</h5>
                        <p class="mb-4">
                            @if(Auth::user()->isAdmin())
                                Here's your admin dashboard overview with real-time statistics and analytics.
                            @else
                                You're successfully logged in to your dashboard. Check your account information and manage your settings from here.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 200px;">
                        @php
                            $kycPhoto = Auth::user()->kycApplication && Auth::user()->kycApplication->photograph_path 
                                ? asset('storage/' . Auth::user()->kycApplication->photograph_path) 
                                : null;
                            $profileImage = $kycPhoto ?? Auth::user()->avatar;
                        @endphp
                        
                        @if($profileImage)
                            <img src="{{ $profileImage }}" alt="{{ Auth::user()->name }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #696cff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                        @else
                            <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 150px; height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 5px solid #696cff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <span style="font-size: 60px; font-weight: bold; color: white;">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->isAdmin())
    <!-- Admin Dashboard Statistics -->
    
    <!-- Statistics Cards Row 1 - Users -->
    <div class="row dashboard-row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Total Users</span>
                            <h3 class="card-title mb-1">{{ number_format($totalUsers ?? 0) }}</h3>
                            @if(isset($userGrowth) && $userGrowth != 0)
                                <small class="{{ $userGrowth > 0 ? 'text-success' : 'text-danger' }}">
                                    <i class='bx {{ $userGrowth > 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}'></i>
                                    {{ abs($userGrowth) }}% this month
                                </small>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-primary" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-group' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Verified Users</span>
                            <h3 class="card-title mb-1">{{ number_format($verifiedUsers ?? 0) }}</h3>
                            <small class="text-muted">
                                {{ $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 1) : 0 }}% verified
                            </small>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-success" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-check-shield' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Unverified Users</span>
                            <h3 class="card-title mb-1">{{ number_format($unverifiedUsers ?? 0) }}</h3>
                            <small class="text-muted">
                                {{ $totalUsers > 0 ? round(($unverifiedUsers / $totalUsers) * 100, 1) : 0 }}% pending
                            </small>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-warning" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-shield-x' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">New This Month</span>
                            <h3 class="card-title mb-1">{{ number_format($newUsersThisMonth ?? 0) }}</h3>
                            <small class="text-muted">User registrations</small>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-info" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-user-plus' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 2 - Loans -->
    <div class="row dashboard-row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Total Loans</span>
                            <h3 class="card-title mb-1">{{ number_format($totalLoans ?? 0) }}</h3>
                            <small class="text-muted">₹{{ number_format($totalLoanAmount ?? 0, 0) }} total</small>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-primary" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-credit-card' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Approved Loans</span>
                            <h3 class="card-title mb-1 text-success">{{ number_format($approvedLoans ?? 0) }}</h3>
                            <small class="text-muted">₹{{ number_format($approvedLoanAmount ?? 0, 0) }}</small>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-success" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-check-circle' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Pending Loans</span>
                            <h3 class="card-title mb-1 text-warning">{{ number_format($pendingLoans ?? 0) }}</h3>
                            <small class="text-muted">₹{{ number_format($pendingLoanAmount ?? 0, 0) }}</small>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-warning" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-time-five' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Completed Loans</span>
                            <h3 class="card-title mb-1 text-success">{{ number_format($completedLoans ?? 0) }}</h3>
                            <small class="text-muted">Fully paid</small>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-success" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-check' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 3 - Collections -->
    <div class="row dashboard-row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Today's Collection</span>
                            <h3 class="card-title mb-1 text-success">₹{{ number_format($dailyCollection ?? 0, 2) }}</h3>
                            @if($dailyLateFees > 0)
                                <small class="text-danger">Late fees: ₹{{ number_format($dailyLateFees, 2) }}</small>
                            @else
                                <small class="text-muted">No late fees today</small>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-success" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-money' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Monthly Collection</span>
                            <h3 class="card-title mb-1 text-success">₹{{ number_format($monthlyCollection ?? 0, 2) }}</h3>
                            @if($monthlyLateFees > 0)
                                <small class="text-danger">Late fees: ₹{{ number_format($monthlyLateFees, 2) }}</small>
                            @else
                                <small class="text-muted">This month</small>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-primary" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-rupee' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Total Collection</span>
                            <h3 class="card-title mb-1">₹{{ number_format($totalCollection ?? 0, 2) }}</h3>
                            <small class="text-muted">All time</small>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-info" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-wallet' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card statistics-card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Overdue Payments</span>
                            <h3 class="card-title mb-1 text-danger">{{ number_format($pendingPayments ?? 0) }}</h3>
                            <small class="text-danger">₹{{ number_format($pendingPaymentAmount ?? 0, 2) }} due</small>
                            @if(isset($delayedPayments) && $delayedPayments > 0)
                                <small class="text-muted d-block mt-1">{{ $delayedPayments }} delayed</small>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <div class="avatar avatar-lg bg-label-danger" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class='bx bx-alarm' style="font-size: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Growth Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Growth Overview (Last 6 Months)</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class='bx bx-calendar'></i> Last 6 Months
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="growthChart"></div>
                </div>
            </div>
        </div>

        <!-- Status Distribution Pie Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Loan Status</h5>
                </div>
                <div class="card-body">
                    <div id="statusChart"></div>
                </div>
            </div>
        </div>
    </div>


@else
    <!-- Regular User Dashboard -->
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-6 order-0 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Account Information</h5>
                        <small class="text-muted">Your account details</small>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-user'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Name</h6>
                                    <small class="text-muted">Full name</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">{{ Auth::user()->name }}</small>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class='bx bx-envelope'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Email</h6>
                                    <small class="text-muted">Email address</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">{{ Auth::user()->email }}</small>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class='bx bx-calendar'></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Member Since</h6>
                                    <small class="text-muted">Registration date</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">{{ Auth::user()->created_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('vendor-scripts')
<script src="{{ asset('sneat/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush

@push('scripts')
@if(Auth::user()->isAdmin())
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Growth Chart - Loans and Collections
    const collectionsDataRaw = @json($collectionsChartData ?? []);
    const collectionsData = collectionsDataRaw.map(amount => parseFloat(amount || 0));
    const growthChartOptions = {
        series: [{
            name: 'Loans',
            data: @json($loansChartData ?? [])
        }, {
            name: 'Collections (₹)',
            data: collectionsData
        }],
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: false
            }
        },
        colors: ['#696cff', '#03c3ec'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: @json($months ?? []),
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        },
        grid: {
            borderColor: '#f1f1f1',
        }
    };
    
    const growthChart = new ApexCharts(document.querySelector("#growthChart"), growthChartOptions);
    growthChart.render();

    // Status Distribution Pie Chart
    const statusChartOptions = {
        series: Object.values(@json($statusDistribution ?? [])),
        chart: {
            type: 'donut',
            height: 300
        },
        labels: Object.keys(@json($statusDistribution ?? [])),
        colors: ['#ffab00', '#71dd37', '#03c3ec', '#ff3e1d'],
        legend: {
            position: 'bottom'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '16px'
                        },
                        value: {
                            show: true,
                            fontSize: '24px',
                            fontWeight: 'bold'
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => {
                                    return a + b
                                }, 0)
                            }
                        }
                    }
                }
            }
        }
    };
    
    const statusChart = new ApexCharts(document.querySelector("#statusChart"), statusChartOptions);
    statusChart.render();
});
</script>
@endif
@endpush
