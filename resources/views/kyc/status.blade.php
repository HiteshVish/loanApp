@extends('layouts.kyc-minimal')

@section('title', 'KYC Status')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        @if($application->status === 'pending' || $application->status === 'under_review')
        <!-- Pending Status -->
        <div class="text-center py-5">
                <div class="mb-4">
                    <div class="avatar avatar-xl mx-auto mb-3" style="width: 100px; height: 100px;">
                        <span class="avatar-initial rounded-circle bg-label-warning" style="font-size: 48px;">
                            <i class="bx bx-time-five"></i>
                        </span>
                    </div>
                </div>
                <h3 class="mb-2">Your Account is Under KYC Verification</h3>
                <p class="text-muted mb-4">
                    Your KYC application has been submitted successfully and is currently being reviewed by our team. This usually takes 1-2 business days.
                </p>
                <div class="alert alert-warning">
                    <h6 class="alert-heading mb-2">
                        <i class="bx bx-info-circle"></i> Application Status: 
                        <span class="badge bg-warning ms-2">{{ ucfirst($application->status) }}</span>
                    </h6>
                    <p class="mb-0">Submitted on: {{ $application->created_at->format('F d, Y \a\t h:i A') }}</p>
                </div>
        </div>
        @elseif($application->status === 'approved')
        <!-- Approved Status -->
        <div class="text-center py-5">
                <div class="mb-4">
                    <div class="avatar avatar-xl mx-auto mb-3" style="width: 100px; height: 100px;">
                        <span class="avatar-initial rounded-circle bg-label-success" style="font-size: 48px;">
                            <i class="bx bx-check-circle"></i>
                        </span>
                    </div>
                </div>
                <h3 class="mb-2 text-success">KYC Approved!</h3>
                <p class="text-muted mb-4">
                    Congratulations! Your KYC application has been approved. You can now access your dashboard.
                </p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                    <i class="bx bx-home-circle"></i> Go to Dashboard
                </a>
        </div>
        @elseif($application->status === 'rejected')
        <!-- Rejected Status -->
        <div class="text-center py-5">
                <div class="mb-4">
                    <div class="avatar avatar-xl mx-auto mb-3" style="width: 100px; height: 100px;">
                        <span class="avatar-initial rounded-circle bg-label-danger" style="font-size: 48px;">
                            <i class="bx bx-x-circle"></i>
                        </span>
                    </div>
                </div>
                <h3 class="mb-2 text-danger">KYC Application Rejected</h3>
                <p class="text-muted mb-4">
                    Unfortunately, your KYC application was not approved. Please contact support for more information.
                </p>
                @if($application->admin_notes)
                <div class="alert alert-danger">
                    <h6 class="alert-heading mb-2">Rejection Reason:</h6>
                    <p class="mb-0">{{ $application->admin_notes }}</p>
                </div>
                @endif
                <div class="mt-4">
                    <a href="mailto:support@example.com" class="btn btn-primary me-2">
                        <i class="bx bx-envelope"></i> Contact Support
                    </a>
                </div>
        </div>
        @endif
    </div>
</div>
@endsection
