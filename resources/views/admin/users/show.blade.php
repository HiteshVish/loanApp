@extends('layouts.sneat')

@section('title', 'User Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin / Users /</span> {{ $user->name }}
    </h4>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back"></i> Back to Users
    </a>
</div>

<!-- User Basic Information -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bx bx-user me-2"></i>User Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">User ID</label>
                <p class="fw-bold">#{{ $user->id }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Email</label>
                <p class="fw-bold">{{ $user->email }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Name</label>
                <p class="fw-bold">{{ $user->name }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Role</label>
                <p>
                    @if($user->isAdmin())
                        <span class="badge bg-danger">Admin</span>
                    @else
                        <span class="badge bg-primary">User</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Email Status</label>
                <p>
                    @if($user->email_verified_at)
                        <span class="badge bg-success">Verified</span>
                    @else
                        <span class="badge bg-warning">Unverified</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Joined On</label>
                <p>{{ $user->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- User Detail Information -->
@if($user->userDetail)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bx bx-info-circle me-2"></i>Additional Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Mobile</label>
                <p class="fw-bold">{{ $user->userDetail->mobile ?? '-' }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Aadhar Number</label>
                <p class="fw-bold">{{ $user->userDetail->aadhar ?? '-' }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">PAN Number</label>
                <p class="fw-bold">{{ $user->userDetail->pan ?? '-' }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label text-muted">Date of Birth</label>
                <p>{{ $user->userDetail->date_of_birth ? \Carbon\Carbon::parse($user->userDetail->date_of_birth)->format('M d, Y') : '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- All Loans Information -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bx bx-credit-card me-2"></i>All Loans</h5>
        <span class="badge bg-primary">Total: {{ $user->loanDetails->count() }}</span>
    </div>
    <div class="card-body">
        @if($user->loanDetails->isEmpty())
            <div class="text-center py-5">
                <i class="bx bx-credit-card-alt bx-lg text-muted"></i>
                <p class="text-muted mt-2">No loans found for this user.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Loan Amount</th>
                            <th>Tenure</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Applied Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->loanDetails as $loan)
                        <tr>
                            <td><strong>{{ $loan->loan_id }}</strong></td>
                            <td class="text-success">₹{{ number_format($loan->loan_amount, 2) }}</td>
                            <td><span class="badge bg-label-info">{{ $loan->tenure }} months</span></td>
                            <td>
                                @if($loan->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($loan->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($loan->status === 'completed')
                                    <span class="badge bg-primary">Completed</span>
                                @elseif($loan->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>₹{{ number_format($loan->total_amount_with_interest, 2) }}</td>
                            <td class="text-success">₹{{ number_format($loan->total_paid, 2) }}</td>
                            <td class="text-danger">₹{{ number_format($loan->remaining_amount, 2) }}</td>
                            <td>{{ $loan->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if($loan->status === 'approved')
                                            <a class="dropdown-item" href="{{ route('admin.payment.show', $loan->loan_id) }}">
                                                <i class="bx bx-money me-1"></i> View Payments
                                            </a>
                                        @endif
                                        <a class="dropdown-item" href="{{ route('admin.kyc.show', $loan) }}">
                                            <i class="bx bx-detail me-1"></i> Loan Details
                                        </a>
                                        @if($loan->status === 'approved')
                                            <form action="{{ route('admin.loan.complete', $loan->loan_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this loan as completed?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item text-primary">
                                                    <i class="bx bx-check me-1"></i> Mark as Completed
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Loan Statistics Summary -->
@if($user->loanDetails->isNotEmpty())
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="bx bx-credit-card"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Total Loans</p>
                        <h5 class="mb-0">{{ $user->loanDetails->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="bx bx-check"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Approved</p>
                        <h5 class="mb-0">{{ $user->loanDetails->where('status', 'approved')->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="bx bx-time"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Pending</p>
                        <h5 class="mb-0">{{ $user->loanDetails->where('status', 'pending')->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="bx bx-check-circle"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Completed</p>
                        <h5 class="mb-0">{{ $user->loanDetails->where('status', 'completed')->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

