@extends('layouts.sneat')

@section('title', 'KYC Applications')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

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
        <a href="javascript:void(0)" class="text-decoration-none filter-card" data-status="">
            <div class="card border-primary" style="cursor: pointer; transition: all 0.3s;">
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
        <a href="javascript:void(0)" class="text-decoration-none filter-card" data-status="pending">
            <div class="card" style="cursor: pointer; transition: all 0.3s;">
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
        <a href="javascript:void(0)" class="text-decoration-none filter-card" data-status="approved">
            <div class="card" style="cursor: pointer; transition: all 0.3s;">
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
        <a href="javascript:void(0)" class="text-decoration-none filter-card" data-status="rejected">
            <div class="card" style="cursor: pointer; transition: all 0.3s;">
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
        <span id="tableTitle">All KYC Applications</span>
        <span class="badge bg-primary" id="totalKycBadge">Loading...</span>
    </h5>
    <div class="table-responsive text-nowrap">
        <table id="kycTable" class="table table-striped" style="width:100%">
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
                <!-- DataTables will populate this -->
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    let currentStatus = '';
    
    const table = $('#kycTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.kyc.data') }}",
            type: 'GET',
            data: function(d) {
                d.status = currentStatus;
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables error:', error);
            }
        },
        columns: [
            { 
                data: 'loan_id',
                name: 'loan_id',
                render: function(data) {
                    return '<strong>' + data + '</strong>';
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
                    
                    return '<div class="d-flex align-items-center">' + avatar + '<div><strong>' + data + '</strong><br><small class="text-muted">' + row.email + '</small></div></div>';
                }
            },
            { 
                data: 'loan_amount',
                name: 'loan_amount',
                render: function(data) {
                    return '₹' + parseFloat(data).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            },
            { 
                data: 'tenure',
                name: 'tenure',
                render: function(data) {
                    return '<span class="badge bg-label-info">' + data + ' months</span>';
                }
            },
            { 
                data: 'created_at',
                name: 'created_at'
            },
            { 
                data: 'status',
                name: 'status',
                render: function(data) {
                    let badgeClass = 'bg-secondary';
                    let badgeText = data.charAt(0).toUpperCase() + data.slice(1);
                    
                    if (data === 'pending') {
                        badgeClass = 'bg-warning';
                    } else if (data === 'approved') {
                        badgeClass = 'bg-success';
                    } else if (data === 'rejected') {
                        badgeClass = 'bg-danger';
                    }
                    
                    return '<span class="badge ' + badgeClass + '">' + badgeText + '</span>';
                }
            },
            { 
                data: 'loan_id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    // Use loan_id (which is the primary key) for route model binding
                    const loanId = data || row.loan_id || row.id;
                    
                    if (!loanId) {
                        console.error('Loan ID is missing for row:', row);
                        return '<span class="text-danger">Error: Missing ID</span>';
                    }
                    
                    let actions = '<div class="d-flex gap-2">';
                    
                    // Review button (using loan_id for route model binding)
                    actions += '<a href="{{ url("admin/kyc") }}/' + loanId + '" class="btn btn-sm btn-label-primary" title="Review"><i class="bx bx-show"></i></a>';
                    
                    // Delete button (using loan_id for route model binding)
                    actions += '<form action="{{ url("admin/kyc") }}/' + loanId + '" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this loan?\');">';
                    actions += '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                    actions += '<input type="hidden" name="_method" value="DELETE">';
                    actions += '<button type="submit" class="btn btn-sm btn-label-danger" title="Delete"><i class="bx bx-trash"></i></button>';
                    actions += '</form>';
                    
                    actions += '</div>';
                    return actions;
                }
            }
        ],
        order: [[4, 'desc']], // Default order by created_at desc
        pageLength: 20,
        responsive: true,
        language: {
            processing: "Loading applications...",
            search: "Search:",
            lengthMenu: "Show _MENU_ applications per page",
            info: "Showing _START_ to _END_ of _TOTAL_ applications",
            infoEmpty: "No applications found",
            infoFiltered: "(filtered from _MAX_ total applications)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        drawCallback: function(settings) {
            // Update total badge
            const api = this.api();
            const total = api.page.info().recordsTotal;
            $('#totalKycBadge').text(total + ' Total Applications');
        }
    });
    
    // Handle filter card clicks
    $('.filter-card').on('click', function() {
        const status = $(this).data('status');
        currentStatus = status;
        
        // Update card borders
        $('.filter-card .card').removeClass('border-primary border-warning border-success border-danger');
        if (status === '') {
            $(this).find('.card').addClass('border-primary');
        } else if (status === 'pending') {
            $(this).find('.card').addClass('border-warning');
        } else if (status === 'approved') {
            $(this).find('.card').addClass('border-success');
        } else if (status === 'rejected') {
            $(this).find('.card').addClass('border-danger');
        }
        
        // Update table title
        if (status === '') {
            $('#tableTitle').text('All KYC Applications');
        } else {
            $('#tableTitle').html(ucfirst(status) + ' KYC Applications <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary ms-2 clear-filter"><i class="bx bx-x"></i> Clear Filter</a>');
        }
        
        // Reload table with new filter
        table.ajax.reload();
    });
    
    // Handle clear filter
    $(document).on('click', '.clear-filter', function() {
        currentStatus = '';
        $('.filter-card .card').removeClass('border-primary border-warning border-success border-danger');
        $('.filter-card[data-status=""]').find('.card').addClass('border-primary');
        $('#tableTitle').text('All KYC Applications');
        table.ajax.reload();
    });
    
    // Helper function
    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
});
</script>
@endpush
@endsection
