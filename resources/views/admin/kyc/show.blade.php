@extends('layouts.sneat')

@section('title', 'Review KYC Application')

@section('content')
@php
    $loan = $loan;
    $userDetail = $loan->user->userDetail;
    $status = $loan->status;
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin / KYC /</span> {{ $loan->loan_id }}
    </h4>
    <div>
        @if($status === 'pending')
            <span class="badge bg-warning me-2">Pending</span>
        @elseif($status === 'approved')
            <span class="badge bg-success me-2">Approved</span>
        @elseif($status === 'rejected')
            <span class="badge bg-danger me-2">Rejected</span>
        @else
            <span class="badge bg-secondary me-2">{{ ucfirst($status) }}</span>
        @endif
        <a href="{{ route('admin.kyc.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Applicant Info -->
    <div class="col-md-4 mb-4">
        <!-- Profile Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                @php
                    $name = $userDetail ? $userDetail->name : $loan->user->name;
                    $email = $userDetail ? $userDetail->email : $loan->user->email;
                @endphp
                @if($userDetail && $userDetail->photo)
                    <img src="{{ url('storage/app/public/' . $userDetail->photo) }}" alt class="rounded mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @elseif($loan->user->avatar)
                    <img src="{{ $loan->user->avatar }}" alt class="rounded-circle mb-3" width="120">
                @else
                    <div class="avatar avatar-xl mx-auto mb-3" style="width: 120px; height: 120px;">
                        <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 48px;">{{ substr($name, 0, 1) }}</span>
                    </div>
                @endif
                <h5 class="mb-1">{{ $name }}</h5>
                <p class="text-muted mb-3">{{ $email }}</p>
                <div class="mb-3">
                    @if($status === 'pending')
                        <span class="badge bg-warning">Pending</span>
                    @elseif($status === 'approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($status === 'rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                </div>
                <div class="text-start">
                    <p class="mb-1"><strong>User ID:</strong> #{{ $loan->user_id }}</p>
                    <p class="mb-1"><strong>Loan ID:</strong> {{ $loan->loan_id }}</p>
                    <p class="mb-0"><strong>Created:</strong> {{ $loan->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- All Phone Contacts Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bx bx-phone"></i> All Phone Contacts</h6>
                @php
                    $contacts = $loan->user->referencePhones ?? collect();
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
                            <a href="{{ route('admin.kyc.contacts', $loan) }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-show-alt me-1"></i>
                                View All {{ $contacts->count() }} Contacts
                            </a>
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

        <!-- Google Map Location Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bx bx-map"></i> User Locations</h6>
                @php
                    $locations = $loan->user->locations ?? collect();
                @endphp
                @if($locations->count() > 0)
                    <span class="badge bg-label-primary">{{ $locations->count() }} Locations</span>
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
                        <a href="{{ route('admin.kyc.locations', $loan) }}" class="btn btn-primary btn-sm w-100">
                            <i class="bx bx-map-alt me-1"></i>
                            View Full Map & All {{ $locations->count() }} Locations
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Application Details -->
    <div class="col-md-8 mb-4">
        <div class="card mb-4">
            <h5 class="card-header">Personal Details</h5>
            <div class="card-body">
                    <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Full Name:</strong> {{ $name }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Date of Birth:</strong> {{ $userDetail && $userDetail->dob ? $userDetail->dob->format('M d, Y') : 'N/A' }} 
                        @if($userDetail && $userDetail->dob)
                            ({{ $userDetail->dob->age }} years)
                        @endif
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Gender:</strong> {{ $userDetail && $userDetail->gender ? $userDetail->gender : 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Nationality:</strong> {{ $userDetail && $userDetail->nationality ? $userDetail->nationality : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">Contact Information</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Mobile:</strong> {{ $userDetail && $userDetail->mobile ? $userDetail->mobile : $loan->user->phone ?? 'N/A' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Email:</strong> {{ $email }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">Address Details</h5>
            <div class="card-body">
                <h6 class="text-primary">Current Address</h6>
                <p>{{ $userDetail && $userDetail->current_address ? $userDetail->current_address : 'N/A' }}</p>
                
                @if($userDetail && $userDetail->permanent_address)
                <h6 class="text-primary mt-3">Permanent Address</h6>
                <p>{{ $userDetail->permanent_address }}</p>
                @else
                <p class="text-muted"><em>Same as current address</em></p>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">Loan Details</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Amount:</strong> <span class="text-primary">₹{{ number_format($loan->loan_amount, 2) }}</span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Tenure:</strong> {{ $loan->tenure }} months ({{ round($loan->tenure/12, 1) }} years)
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Loan ID:</strong> <span class="badge bg-label-info">{{ $loan->loan_id }}</span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Status:</strong> 
                        @if($loan->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($loan->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($loan->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">KYC Documents</h5>
            <div class="card-body">
                <h6 class="mb-3">Uploaded Documents</h6>
                <div class="row">
                    @if($userDetail && $userDetail->aadhar)
                    <div class="col-md-4 mb-3">
                        <a href="{{ url('storage/app/public/' . $userDetail->aadhar) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bx bx-file"></i> View Aadhar
                        </a>
                    </div>
                    @endif
                    @if($userDetail && $userDetail->pan)
                    <div class="col-md-4 mb-3">
                        <a href="{{ url('storage/app/public/' . $userDetail->pan) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bx bx-file"></i> View PAN
                        </a>
                    </div>
                    @endif
                    @if($userDetail && $userDetail->photo)
                    <div class="col-md-4 mb-3">
                        <a href="{{ url('storage/app/public/' . $userDetail->photo) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bx bx-image"></i> View Photo
                        </a>
                    </div>
                    @endif
                    @if((!$userDetail || (!$userDetail->aadhar && !$userDetail->pan && !$userDetail->photo)))
                    <div class="col-12">
                        <p class="text-muted text-center mb-0">No documents uploaded yet</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($status === 'pending')
        <!-- Approval/Rejection Forms -->
        <div class="row">
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="text-success mb-3"><i class="bx bx-check-circle"></i> Approve Application</h5>
                        <form action="{{ route('admin.kyc.approve', $loan) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Admin Notes (Optional)</label>
                                <textarea name="admin_notes" class="form-control" rows="2" placeholder="Add any notes..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Are you sure you want to approve this application?');">
                                <i class="bx bx-check"></i> Approve KYC
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-body">
                        <h5 class="text-danger mb-3"><i class="bx bx-x-circle"></i> Reject Application</h5>
                        <form action="{{ route('admin.kyc.reject', $loan) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea name="admin_notes" class="form-control" rows="2" placeholder="Reason for rejection..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to reject this application?');">
                                <i class="bx bx-x"></i> Reject KYC
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @elseif($status === 'approved')
        <!-- Approved - Show Disapprove Option -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="text-success"><i class="bx bx-check-circle"></i> Loan Approved</h6>
                <p><strong>Reviewed By:</strong> System</p>
                <p><strong>Reviewed At:</strong> {{ $loan->updated_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
        </div>

        <div class="card border-warning">
            <div class="card-body">
                <h5 class="text-warning mb-3"><i class="bx bx-revision"></i> Change Status</h5>
                <p class="text-muted">If you need to change the approved status, select a new status below:</p>
                <form action="{{ route('admin.kyc.disapprove', $loan) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">New Status <span class="text-danger">*</span></label>
                        <select name="new_status" class="form-select" required>
                            <option value="">Choose new status...</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Change (Optional)</label>
                        <textarea name="admin_notes" class="form-control" rows="2" placeholder="Why are you changing the status?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Are you sure you want to change the status? This will affect user access.');">
                        <i class="bx bx-revision"></i> Change Status
                    </button>
                </form>
            </div>
        </div>
        @else
        <!-- Rejected or Other Status -->
        <div class="card">
            <div class="card-body">
                <h6>Review Information</h6>
                <p><strong>Reviewed At:</strong> {{ $loan->updated_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
@if($locations && $locations->count() > 0)
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
@if($locations && $locations->count() > 0)
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
            <div style="text-align: center;">
                <strong style="color: #696cff;">Location ${index + 1}</strong><br>
                <span style="font-size: 13px;">${location.address || `Lat: ${location.lat}, Long: ${location.lng}`}</span>
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
