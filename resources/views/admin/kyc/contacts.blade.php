@extends('layouts.sneat')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'All Contacts - KYC Application ' . ($loan->loan_id ?? 'LON' . str_pad($loan->id, 3, '0', STR_PAD_LEFT)))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin / KYC / Application {{ $loan->loan_id ?? 'LON' . str_pad($loan->id, 3, '0', STR_PAD_LEFT) }} /</span> All Contacts
    </h4>
    <a href="{{ route('admin.kyc.show', $loan) }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back"></i> Back to Application
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bx bx-error-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-4 mb-4">
        <!-- Application Info -->
        <div class="card">
            <div class="card-body text-center">
                @php
                    $userDetail = $loan->user->userDetail;
                    $name = $userDetail && $userDetail->name ? $userDetail->name : $loan->user->name;
                    $email = $userDetail && $userDetail->email ? $userDetail->email : $loan->user->email;
                    $photo = $userDetail && $userDetail->photo ? $userDetail->photo : null;
                @endphp
                
                @if($loan->user->avatar)
                    <img src="{{ $loan->user->avatar }}" alt class="rounded mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                @elseif($photo)
                    <img src="{{ Storage::url($photo) }}" alt class="rounded mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                @else
                    <div class="avatar avatar-xl mx-auto mb-3">
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

    <!-- Contacts List -->
    <div class="col-md-8 mb-4">
        <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bx bx-list-ul me-2"></i>All Contacts</h5>
        <span class="badge bg-primary" id="contactCount">{{ $loan->user->referencePhones->count() }} Contacts</span>
    </div>
    <div class="card-body p-0" id="allContactsList">
        @if($loan->user->referencePhones && $loan->user->referencePhones->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">no</th>
                            <th>number</th>
                            <th>name</th>
                            <th style="width: 150px;">Added On</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->user->referencePhones as $index => $contact)
                            <tr id="contact-row-{{ $contact->id }}">
                                <td>{{ $index + 1 }}:</td>
                                <td>{{ $contact->contact_number }}</td>
                                <td>{{ $contact->name ?? '-' }}</td>
                                <td>
                                    <small class="text-muted">{{ $contact->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <form action="{{ route('admin.kyc.contacts.delete', ['loan' => $loan, 'contact' => $contact]) }}" method="POST" class="d-inline delete-contact-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-contact-btn" data-contact-number="{{ $contact->contact_number }}" data-contact-name="{{ $contact->name ?? 'N/A' }}">
                                            <i class="bx bx-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div class="avatar avatar-lg bg-label-secondary mb-3">
                    <i class="bx bx-phone-off bx-lg"></i>
                </div>
                <h6 class="mb-1">No contacts available</h6>
                <p class="text-muted small mb-0">Contacts will appear here when added via mobile app</p>
            </div>
        @endif
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete button clicks with confirmation
    const deleteForms = document.querySelectorAll('.delete-contact-form');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = form.querySelector('.delete-contact-btn');
            const contactNumber = button.getAttribute('data-contact-number');
            const contactName = button.getAttribute('data-contact-name');
            
            // Show confirmation prompt
            if (confirm(`Are you sure you want to delete this contact?\n\nContact: ${contactName}\nNumber: ${contactNumber}\n\nThis action cannot be undone.`)) {
                // Disable button and show loading state
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Deleting...';
                
                // Submit the form
                form.submit();
            }
        });
    });
});
</script>
@endpush

@endsection

