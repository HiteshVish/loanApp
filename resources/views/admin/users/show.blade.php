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
                <label class="form-label text-muted">Date of Birth</label>
                <p>{{ $user->userDetail->dob ? \Carbon\Carbon::parse($user->userDetail->dob)->format('M d, Y') : '-' }}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 mb-3">
                <label class="form-label text-muted">Uploaded Documents</label>
                <div class="d-flex gap-2 flex-wrap">
                    @if($user->userDetail->aadhar)
                    <a href="{{ url('storage/app/public/' . $user->userDetail->aadhar) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-file"></i> View Aadhar
                    </a>
                    @endif
                    @if($user->userDetail->pan)
                    <a href="{{ url('storage/app/public/' . $user->userDetail->pan) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-file"></i> View PAN
                    </a>
                    @endif
                    @if($user->userDetail->photo)
                    <a href="{{ url('storage/app/public/' . $user->userDetail->photo) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-image"></i> View Photo
                    </a>
                    @endif
                    @if(!$user->userDetail->aadhar && !$user->userDetail->pan && !$user->userDetail->photo)
                    <p class="text-muted mb-0">No documents uploaded</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- All Phone Contacts -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bx bx-phone me-2"></i>All Phone Contacts</h5>
        @php
            $contacts = $user->referencePhones ?? collect();
        @endphp
        @if($contacts->count() > 0)
            <span class="badge bg-label-primary">{{ $contacts->count() }} Contacts</span>
        @endif
    </div>
    <div class="card-body">
        @if($contacts->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">id</th>
                            <th>number</th>
                            <th>name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts->take(10) as $index => $contact)
                            <tr>
                                <td>{{ $index + 1 }}:</td>
                                <td>{{ $contact->contact_number }}</td>
                                <td>{{ $contact->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($contacts->count() > 10)
                <div class="mt-3 text-center">
                    <p class="text-muted small mb-0">Showing latest 10 of {{ $contacts->count() }} contacts</p>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="d-flex justify-content-center mb-3">
                    <div class="avatar avatar-xl bg-label-secondary">
                        <i class="bx bx-phone-off" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
                <h6 class="mb-2">No contacts available</h6>
                <p class="text-muted small mb-0">Contacts will appear here when added via mobile app</p>
            </div>
        @endif
    </div>
</div>

<!-- User Locations -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bx bx-map me-2"></i>User Locations</h5>
        @php
            $locations = $user->locations ?? collect();
        @endphp
        @if($locations->count() > 0)
            <span class="badge bg-label-primary">Latest {{ $locations->count() }} Locations</span>
        @endif
    </div>
    <div class="card-body p-0">
        @if($locations->count() > 0)
            <div id="locationMap" style="height: 400px; position: relative;"></div>
        @else
            <div class="text-center py-5">
                <div class="d-flex justify-content-center mb-3">
                    <div class="avatar avatar-xl bg-label-secondary">
                        <i class="bx bx-map-alt" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
                <h6 class="mb-2">No location data available</h6>
                <p class="text-muted small mb-0">Locations will appear when added via mobile app</p>
            </div>
        @endif
    </div>
    @if($locations->count() > 0)
        <div class="card-footer">
            <div id="locationsList" class="mb-3">
                @foreach($locations->take(3) as $index => $location)
                    <div class="mb-1">
                        <span class="badge bg-label-primary me-1">{{ $index + 1 }}</span>
                        <small class="text-muted">{{ $location->address ?? 'Lat: ' . $location->latitude . ', Long: ' . $location->longitude }}</small>
                    </div>
                @endforeach
                @if($locations->count() > 3)
                    <div class="text-muted small">
                        <em>... and {{ $locations->count() - 3 }} more locations</em>
                    </div>
                @endif
            </div>
            <div class="text-center">
                <p class="text-muted small mb-0">Showing latest {{ $locations->count() }} locations</p>
            </div>
        </div>
    @endif
</div>

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
                                <div class="d-flex gap-2">
                                    @if($loan->status === 'approved')
                                        <a href="{{ route('admin.payment.show', $loan->loan_id) }}" class="btn btn-sm btn-label-primary" title="View Payments">
                                            <i class="bx bx-money"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.kyc.show', $loan) }}" class="btn btn-sm btn-label-info" title="Loan Details">
                                        <i class="bx bx-detail"></i>
                                    </a>
                                    @if($loan->status === 'approved')
                                        <form action="{{ route('admin.loan.complete', $loan->loan_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to mark this loan as completed?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-label-success" title="Mark as Completed">
                                                <i class="bx bx-check"></i>
                                            </button>
                                        </form>
                                    @endif
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

@push('styles')
@php
    $locations = $user->locations ?? collect();
@endphp
@if($locations->count() > 0)
<!-- Leaflet CSS (Free OpenStreetMap) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.leaflet-popup-content {
    margin: 10px;
    font-family: 'Public Sans', sans-serif;
}
.leaflet-popup-content strong {
    color: #696cff;
}
</style>
@endif
@endpush

@push('scripts')
@php
    $locations = $user->locations ?? collect();
@endphp
@if($locations->count() > 0)
<!-- Leaflet JS (Free OpenStreetMap) -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Initialize OpenStreetMap with multiple locations
document.addEventListener('DOMContentLoaded', function() {
    const locations = @json($locations->map(function($loc) {
        return [
            'lat' => (float)$loc->latitude,
            'lng' => (float)$loc->longitude,
            'address' => $loc->address ?? ''
        ];
    }));
    
    if (locations.length === 0) {
        return;
    }
    
    // Create map centered on first location
    const map = L.map('locationMap').setView([locations[0].lat, locations[0].lng], 12);
    
    // Add OpenStreetMap tiles (FREE!)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);
    
    // Create bounds to fit all markers
    const bounds = [];
    
    // Add markers for all locations
    locations.forEach((location, index) => {
        // Create custom numbered icon
        const numberIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="
                background: #696cff;
                color: white;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 14px;
                border: 3px solid white;
                box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            ">${index + 1}</div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15],
        });
        
        // Add marker
        const marker = L.marker([location.lat, location.lng], {
            icon: numberIcon,
            title: location.address || `Location ${index + 1}`
        }).addTo(map);
        
        // Add popup
        marker.bindPopup(`
            <div style="text-align: center; min-width: 200px;">
                <strong style="color: #696cff; font-size: 15px;">Location ${index + 1}</strong><br>
                <span style="font-size: 13px; color: #5d596c;">${location.address || `Lat: ${location.lat}, Long: ${location.lng}`}</span><br>
                <small style="color: #a1a5b7;">Lat: ${location.lat}, Long: ${location.lng}</small>
            </div>
        `);
        
        // Add to bounds
        bounds.push([location.lat, location.lng]);
    });
    
    // Fit map to show all markers
    if (locations.length > 1) {
        map.fitBounds(bounds, { padding: [50, 50] });
    }
});
</script>
@endif
@endpush

@endsection

