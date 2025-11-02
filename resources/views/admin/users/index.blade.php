@extends('layouts.sneat')

@section('title', 'Manage Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin /</span> Manage Users
    </h4>
</div>

<div class="card">
    <h5 class="card-header d-flex justify-content-between align-items-center">
        <span>All Users</span>
        <div class="input-group" style="width: 350px;">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" class="form-control" id="userSearch" placeholder="Search by name, email or ID...">
            <button class="btn btn-outline-secondary" type="button" id="clearUserSearch" style="display: none;">
                <i class="bx bx-x"></i>
            </button>
        </div>
        <span class="badge bg-primary">{{ $users->total() }} Total Users</span>
    </h5>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($users as $user)
                <tr>
                    <td><strong>#{{ $user->id }}</strong></td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt class="w-px-40 h-auto rounded-circle me-2" />
                            @else
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <strong>{{ $user->name }}</strong>
                                @if($user->id === auth()->id())
                                    <span class="badge bg-label-info ms-1">You</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->isAdmin())
                            <span class="badge bg-label-danger">Admin</span>
                        @else
                            <span class="badge bg-label-primary">User</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($user->email_verified_at)
                            <span class="badge bg-label-success">Verified</span>
                        @else
                            <span class="badge bg-label-warning">Unverified</span>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                                    <i class="bx bx-show me-1"></i> View More
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                                @if(!$user->email_verified_at)
                                <form action="{{ route('admin.users.verify-email', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-success">
                                        <i class="bx bx-check-shield me-1"></i> Verify Email
                                    </button>
                                </form>
                                @endif
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    </div>
    @endif
</div>

<div class="mt-4">
    <div class="alert alert-info" role="alert">
        <h6 class="alert-heading mb-1">Admin Access</h6>
        <p class="mb-0">
            <i class="bx bx-info-circle"></i> As an administrator, you can view and edit all user profiles. Regular users can only access their own profile.
        </p>
    </div>
</div>

@push('scripts')
<script>
// Live search functionality (no page refresh)
const searchInput = document.getElementById('userSearch');
const clearSearchBtn = document.getElementById('clearUserSearch');
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
            const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const role = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const joined = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
            
            const rowText = `${id} ${name} ${email} ${role} ${joined} ${status}`;
            
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
                noResultsRow.innerHTML = '<td colspan="7" class="text-center py-4"><i class="bx bx-search-alt bx-lg text-muted"></i><p class="mb-0 mt-2">No users found for "' + searchValue + '"</p></td>';
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

