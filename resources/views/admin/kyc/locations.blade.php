@extends('layouts.sneat')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'All Locations - KYC Application ' . ($loan->loan_id ?? 'LON' . str_pad($loan->id, 3, '0', STR_PAD_LEFT)))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin / KYC / Application {{ $loan->loan_id ?? 'LON' . str_pad($loan->id, 3, '0', STR_PAD_LEFT) }} /</span> All Locations
    </h4>
    <a href="{{ route('admin.kyc.show', $loan) }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back"></i> Back to Application
    </a>
</div>

<div class="row">
    <!-- Profile Card -->
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                @php
                    $userDetail = $loan->user->userDetail;
                    $name = $userDetail && $userDetail->name ? $userDetail->name : $loan->user->name;
                    $email = $userDetail && $userDetail->email ? $userDetail->email : $loan->user->email;
                    $photo = $userDetail && $userDetail->photo ? $userDetail->photo : null;
                @endphp
                
                @if($loan->user->avatar)
                    <img src="{{ $loan->user->avatar }}" alt="{{ $name }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                @elseif($photo)
                    <img src="{{ Storage::url($photo) }}" alt="{{ $name }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                @else
                    <div class="avatar avatar-xl mb-3 mx-auto">
                        <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 36px;">{{ substr($name, 0, 1) }}</span>
                    </div>
                @endif
                <h5 class="mb-1">{{ $name }}</h5>
                <p class="text-muted mb-3">{{ $email }}</p>
                <div class="text-start">
                    <p class="mb-1"><strong>User ID:</strong> #{{ $loan->user->id }}</p>
                    <p class="mb-1"><strong>Loan ID:</strong> {{ $loan->loan_id ?? 'LON' . str_pad($loan->id, 3, '0', STR_PAD_LEFT) }}</p>
                    <p class="mb-0"><strong>Status:</strong> <span class="badge bg-{{ $loan->status === 'approved' ? 'success' : ($loan->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($loan->status) }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Locations Map & List -->
    <div class="col-md-8 mb-4">
        <!-- Map Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-map me-2"></i>Location Map</h5>
                <span class="badge bg-primary">{{ $loan->user->locations->count() }} Locations</span>
            </div>
            <div class="card-body p-0">
                @if($loan->user->locations && $loan->user->locations->count() > 0)
                    <div id="locationMap" style="height: 500px; position: relative;"></div>
                @else
                    <div class="text-center py-5">
                        <div class="avatar avatar-lg bg-label-secondary mb-3">
                            <i class="bx bx-map-alt bx-lg"></i>
                        </div>
                        <h6 class="mb-1">No location data available</h6>
                        <p class="text-muted small mb-0">Locations will appear when added via mobile app</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Locations Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bx bx-list-ul me-2"></i>All Locations</h5>
            </div>
            <div class="card-body p-0">
                @if($loan->user->locations && $loan->user->locations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">no</th>
                                    <th>address</th>
                                    <th style="width: 150px;">latitude</th>
                                    <th style="width: 150px;">longitude</th>
                                    <th style="width: 150px;">Added On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loan->user->locations as $index => $location)
                                    <tr>
                                        <td>{{ $index + 1 }}:</td>
                                        <td>{{ $location->address ?? '-' }}</td>
                                        <td><code>{{ $location->latitude }}</code></td>
                                        <td><code>{{ $location->longitude }}</code></td>
                                        <td>
                                            <small class="text-muted">{{ $location->created_at->format('M d, Y') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="avatar avatar-lg bg-label-secondary mb-3">
                            <i class="bx bx-map-alt bx-lg"></i>
                        </div>
                        <h6 class="mb-1">No location data available</h6>
                        <p class="text-muted small mb-0">Locations will appear when added via mobile app</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
@if($loan->user->locations && $loan->user->locations->count() > 0)
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
@if($loan->user->locations && $loan->user->locations->count() > 0)
<!-- Leaflet JS (Free OpenStreetMap) -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Initialize OpenStreetMap with multiple locations
document.addEventListener('DOMContentLoaded', function() {
    const locations = @json($loan->user->locations->map(function($loc) {
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
    const map = L.map('locationMap').setView([locations[0].lat, locations[0].lng], 5);
    
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
                width: 35px;
                height: 35px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 16px;
                border: 3px solid white;
                box-shadow: 0 3px 8px rgba(0,0,0,0.4);
            ">${index + 1}</div>`,
            iconSize: [35, 35],
            iconAnchor: [17, 17],
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

