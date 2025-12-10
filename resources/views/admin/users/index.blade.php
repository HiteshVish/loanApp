@extends('layouts.sneat')

@section('title', 'Manage Users')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin /</span> Manage Users
    </h4>
</div>

<div class="card">
    <h5 class="card-header d-flex justify-content-between align-items-center">
        <span>All Users</span>
        <span class="badge bg-primary" id="totalUsersBadge">Loading...</span>
    </h5>
    <div class="table-responsive text-nowrap">
        <table id="usersTable" class="table table-striped" style="width:100%">
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
                <!-- DataTables will populate this -->
            </tbody>
        </table>
    </div>
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
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    const table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.users.data') }}",
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('DataTables error:', error);
            }
        },
        columns: [
            { 
                data: 'id',
                name: 'id',
                render: function(data) {
                    return '<strong>#' + data + '</strong>';
                }
            },
            { 
                data: 'name',
                name: 'name',
                render: function(data, type, row) {
                    let avatar = '';
                    if (row.avatar) {
                        avatar = '<img src="' + row.avatar + '" alt class="w-px-40 h-auto rounded-circle me-2" />';
                    } else {
                        const initial = data.charAt(0).toUpperCase();
                        avatar = '<div class="avatar avatar-sm me-2"><span class="avatar-initial rounded-circle bg-label-primary">' + initial + '</span></div>';
                    }
                    
                    let badge = '';
                    if (row.is_current_user) {
                        badge = '<span class="badge bg-label-info ms-1">You</span>';
                    }
                    
                    return '<div class="d-flex align-items-center">' + avatar + '<div><strong>' + data + '</strong>' + badge + '</div></div>';
                }
            },
            { 
                data: 'email',
                name: 'email'
            },
            { 
                data: 'role',
                name: 'role',
                render: function(data, type, row) {
                    if (row.is_admin) {
                        return '<span class="badge bg-label-danger">Admin</span>';
                    } else {
                        return '<span class="badge bg-label-primary">User</span>';
                    }
                }
            },
            { 
                data: 'created_at',
                name: 'created_at'
            },
            { 
                data: 'email_verified_at',
                name: 'email_verified_at',
                render: function(data) {
                    if (data === 'verified') {
                        return '<span class="badge bg-label-success">Verified</span>';
                    } else {
                        return '<span class="badge bg-label-warning">Unverified</span>';
                    }
                }
            },
            { 
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let actions = '<div class="d-flex gap-2">';
                    
                    // View button
                    actions += '<a href="{{ url("admin/users") }}/' + data + '" class="btn btn-sm btn-label-primary" title="View More"><i class="bx bx-show"></i></a>';
                    
                    // Edit button
                    actions += '<a href="{{ url("admin/users") }}/' + data + '/edit" class="btn btn-sm btn-label-info" title="Edit"><i class="bx bx-edit-alt"></i></a>';
                    
                    // Verify Email button (if unverified)
                    if (row.email_verified_at !== 'verified') {
                        actions += '<form action="{{ url("admin/users") }}/' + data + '/verify-email" method="POST" class="d-inline">';
                        actions += '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                        actions += '<button type="submit" class="btn btn-sm btn-label-success" title="Verify Email"><i class="bx bx-check-shield"></i></button>';
                        actions += '</form>';
                    }
                    
                    // Delete button (if not current user)
                    if (!row.is_current_user) {
                        actions += '<form action="{{ url("admin/users") }}/' + data + '" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this user?\');">';
                        actions += '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                        actions += '<input type="hidden" name="_method" value="DELETE">';
                        actions += '<button type="submit" class="btn btn-sm btn-label-danger" title="Delete"><i class="bx bx-trash"></i></button>';
                        actions += '</form>';
                    }
                    
                    actions += '</div>';
                    return actions;
                }
            }
        ],
        order: [[4, 'desc']], // Default order by created_at desc
        pageLength: 15,
        responsive: true,
        language: {
            processing: "Loading users...",
            search: "Search:",
            lengthMenu: "Show _MENU_ users per page",
            info: "Showing _START_ to _END_ of _TOTAL_ users",
            infoEmpty: "No users found",
            infoFiltered: "(filtered from _MAX_ total users)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        drawCallback: function(settings) {
            // Update total users badge
            const api = this.api();
            const total = api.page.info().recordsTotal;
            $('#totalUsersBadge').text(total + ' Total Users');
        }
    });
});
</script>
@endpush
@endsection

