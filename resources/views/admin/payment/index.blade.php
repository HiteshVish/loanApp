@extends('layouts.sneat')

@section('title', 'Payment Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin /</span> Payment Management
    </h4>
</div>

<!-- Info Banner -->
<div class="alert alert-primary mb-3">
    <i class="bx bx-info-circle me-2"></i>
    <strong>Note:</strong> This page displays only <strong>Approved Loans</strong> for payment tracking and management.
</div>

<!-- Payment Calculation Info -->
<div class="alert alert-info mb-4">
    <h6 class="alert-heading"><i class="bx bx-info-circle me-2"></i> Payment Calculation Formula</h6>
    <hr>
    <div class="row">
        <div class="col-md-6 mb-2">
            <strong>Processing Fee:</strong> 5% of loan amount (deducted from disbursement)
        </div>
        <div class="col-md-6 mb-2">
            <strong>Interest Rate:</strong> 15% per 3-month period
        </div>
        <div class="col-md-6 mb-2">
            <strong>Daily EMI:</strong> Total amount (with interest) ÷ (tenure in months × 30 days)
        </div>
        <div class="col-md-6 mb-2">
            <strong>Late Fee:</strong> 0.5% of loan amount per day (applied after 3 missed days)
        </div>
    </div>
    <hr class="my-2">
    <small class="text-muted"><strong>Example:</strong> Loan ₹100 → ₹5 processing fee → ₹95 in-hand → ₹115 total (₹15 interest for 3 months) → ₹3.83/day EMI → ₹0.50/day late fee after 3 days</small>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="bx bx-money"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Total Loans</p>
                        <h5 class="mb-0">{{ $totalLoans }}</h5>
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
                            <i class="bx bx-rupee"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Total Amount</p>
                        <h5 class="mb-0">₹{{ number_format($totalAmount, 2) }}</h5>
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
                        <h5 class="mb-0">{{ $pendingLoans }}</h5>
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
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="bx bx-calendar"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">This Month</p>
                        <h5 class="mb-0">{{ $thisMonthLoans }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loans Table -->
<div class="card">
    <h5 class="card-header d-flex justify-content-between align-items-center">
        <span>All Loans</span>
        <div class="input-group" style="width: 300px;">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" class="form-control" id="loanSearch" placeholder="Search by Loan ID, name or email...">
        </div>
    </h5>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Customer</th>
                    <th>Loan Details</th>
                    <th>Payment Info</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($loans as $loan)
                <tr>
                    <td>
                        <strong>{{ $loan->loan_id }}</strong>
                        <br><small class="text-muted">{{ $loan->created_at->format('M d, Y') }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @php
                                $userDetail = $loan->user->userDetail;
                                $name = $userDetail ? $userDetail->name : $loan->user->name;
                                $email = $userDetail ? $userDetail->email : $loan->user->email;
                            @endphp
                            @if($loan->user->avatar)
                                <img src="{{ $loan->user->avatar }}" alt class="w-px-40 h-auto rounded-circle me-2" />
                            @else
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <strong>{{ $name }}</strong>
                                    @if($loan->delayed_transactions >= 3)
                                        <span class="badge bg-danger">Payment Delayed</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">{{ $email }}</small>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="min-width: 250px;">
                        <div class="small">
                            <strong>Loan Amount:</strong> <span class="text-success">₹{{ number_format($loan->loan_amount, 2) }}</span><br>
                            <strong>Processing Fee (5%):</strong> ₹{{ number_format($loan->processing_fee, 2) }}<br>
                            <strong>In-Hand:</strong> ₹{{ number_format($loan->in_hand_amount, 2) }}<br>
                            <strong>Total with Interest (15%/3m):</strong> ₹{{ number_format($loan->total_amount_with_interest, 2) }}<br>
                            <strong>Tenure:</strong> <span class="badge bg-label-info">{{ $loan->tenure }} months</span>
                        </div>
                    </td>
                    <td style="min-width: 200px;">
                        <div class="small">
                            <strong>Daily EMI:</strong> ₹{{ number_format($loan->daily_emi, 2) }}<br>
                            <strong>Late Fee/Day:</strong> ₹{{ number_format($loan->late_fee_per_day, 2) }}<br>
                            <strong>Transactions:</strong> 
                            <span class="badge bg-success">{{ $loan->completed_transactions }}</span>
                            <span class="badge bg-warning">{{ $loan->pending_transactions }}</span>
                            <span class="badge bg-danger">{{ $loan->delayed_transactions }}</span><br>
                            <strong>Total Paid:</strong> ₹{{ number_format($loan->total_paid, 2) }}<br>
                            <strong>Remaining:</strong> <span class="text-danger">₹{{ number_format($loan->remaining_amount, 2) }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.payment.show', $loan->loan_id) }}" class="btn btn-sm btn-label-primary" title="View Payment Details">
                                <i class="bx bx-money"></i>
                            </a>
                            <a href="{{ route('admin.kyc.show', $loan) }}" class="btn btn-sm btn-label-info" title="View Loan Details">
                                <i class="bx bx-detail"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <i class="bx bx-search-alt bx-lg text-muted"></i>
                        <p class="mb-0 mt-2">No loans found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($loans->hasPages())
    <div class="card-footer">
        {{ $loans->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Search functionality
    const searchInput = document.getElementById('loanSearch');
    const tableRows = document.querySelectorAll('.table tbody tr');

    // Handle search input
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase().trim();
            let visibleCount = 0;

            tableRows.forEach(row => {
                // Skip empty state row
                if (row.querySelector('td[colspan]')) {
                    return;
                }

                // Get text from row
                const loanId = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const customer = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

                const rowText = `${loanId} ${customer}`;

                // Show/hide row based on search
                if (rowText.includes(searchValue)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show "no results" message if nothing found
            const tbody = document.querySelector('.table tbody');
            let noResultsRow = tbody.querySelector('.no-results-row');

            if (visibleCount === 0 && searchValue) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = `
                        <td colspan="5" class="text-center py-4">
                            <i class="bx bx-search-alt bx-lg text-muted"></i>
                            <p class="mb-0 mt-2">No loans found matching your search</p>
                        </td>
                    `;
                    tbody.appendChild(noResultsRow);
                }
                noResultsRow.style.display = '';
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        });
    }
</script>
@endpush
@endsection

