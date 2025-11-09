@extends('layouts.sneat')

@section('title', 'KYC Applications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin /</span> KYC Applications
    </h4>
    <a href="{{ route('admin.kyc.create') }}" class="btn btn-primary">
        <i class="bx bx-plus"></i> Create New Application
    </a>
</div>

<!-- Statistics Cards - Clickable Filters -->
<div class="row mb-4">
    <div class="col-md-3">
        <a href="{{ route('admin.kyc.index') }}" class="text-decoration-none">
            <div class="card {{ !request('status') ? 'border-primary' : '' }}" style="cursor: pointer; transition: all 0.3s;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-file"></i></span>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Total</p>
                            <h5 class="mb-0">{{ $stats['total'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.kyc.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card {{ request('status') === 'pending' ? 'border-warning' : '' }}" style="cursor: pointer; transition: all 0.3s;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-time"></i></span>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Pending</p>
                            <h5 class="mb-0">{{ $stats['pending'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.kyc.index', ['status' => 'approved']) }}" class="text-decoration-none">
            <div class="card {{ request('status') === 'approved' ? 'border-success' : '' }}" style="cursor: pointer; transition: all 0.3s;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-circle"></i></span>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Approved</p>
                            <h5 class="mb-0">{{ $stats['approved'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.kyc.index', ['status' => 'rejected']) }}" class="text-decoration-none">
            <div class="card {{ request('status') === 'rejected' ? 'border-danger' : '' }}" style="cursor: pointer; transition: all 0.3s;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-x-circle"></i></span>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Rejected</p>
                            <h5 class="mb-0">{{ $stats['rejected'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Applications Table -->
<div class="card">
    <h5 class="card-header d-flex justify-content-between align-items-center">
        <span>
            @if(request('status'))
                {{ ucfirst(request('status')) }} KYC Applications
                <a href="{{ route('admin.kyc.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                    <i class="bx bx-x"></i> Clear Filter
                </a>
            @else
                All KYC Applications
            @endif
        </span>
        <div class="input-group" style="width: 350px;">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" class="form-control" id="kycSearch" placeholder="Search by name, email or Loan ID...">
            <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none;">
                <i class="bx bx-x"></i>
            </button>
        </div>
    </h5>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Applicant</th>
                    <th>Loan Amount</th>
                    <th>Tenure</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($loans as $loan)
                <tr>
                    <td><strong>{{ $loan->loan_id }}</strong></td>
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
                                <strong>{{ $name }}</strong>
                                <br><small class="text-muted">{{ $email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>₹{{ number_format($loan->loan_amount, 2) }}</td>
                    <td><span class="badge bg-label-info">{{ $loan->tenure }} months</span></td>
                    <td>{{ $loan->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($loan->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($loan->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($loan->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($loan->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.kyc.show', $loan) }}" class="btn btn-sm btn-label-primary" title="Review">
                                <i class="bx bx-show"></i>
                            </a>
                            <form action="{{ route('admin.kyc.destroy', $loan) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this loan?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-label-danger" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No KYC applications found.</td>
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
// Live search functionality (no page refresh)
const searchInput = document.getElementById('kycSearch');
const clearSearchBtn = document.getElementById('clearSearch');
const tableRows = document.querySelectorAll('.table tbody tr');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        const searchValue = this.value.toLowerCase().trim();
        let visibleCount = 0;
        
        // Show/hide clear button
        clearSearchBtn.style.display = searchValue ? 'block' : 'none';
        
        // Filter table rows
        tableRows.forEach(row => {
            // Skip empty state row
            if (row.querySelector('td[colspan]')) {
                return;
            }
            
            // Get searchable text from row
            const id = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const applicantName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const loanAmount = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const purpose = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const submitted = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
            
            const rowText = `${id} ${applicantName} ${loanAmount} ${purpose} ${submitted} ${status}`;
            
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
                noResultsRow.innerHTML = '<td colspan="7" class="text-center py-4"><i class="bx bx-search-alt bx-lg text-muted"></i><p class="mb-0 mt-2">No applications found for "' + searchValue + '"</p></td>';
                tbody.appendChild(noResultsRow);
            }
            noResultsRow.style.display = '';
        } else if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
    });
    
    // Clear search button
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });
    }
}
</script>
@endpush
@endsection
