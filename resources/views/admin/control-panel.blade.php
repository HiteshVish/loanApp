@extends('layouts.sneat')

@section('title', 'Control Panel')

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-start">
                    <div class="d-flex align-items-center justify-content-center me-3" style="width: 64px; height: 64px; flex-shrink: 0;">
                        <div class="avatar avatar-xl bg-label-primary" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                            <i class='bx bx-cog' style="font-size: 2rem; display: flex; align-items: center; justify-content: center;"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-1">Control Panel</h4>
                        <p class="mb-0 text-muted">System configuration and management tools</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row dashboard-row">
                    <!-- Interest Rate Setting -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card quick-action-card border rounded p-4" style="cursor: pointer;" onclick="openInterestRateModal()">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                <div class="d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                    <div class="avatar avatar-lg bg-label-success" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                        <i class='bx bx-calculator' style="font-size: 2rem; display: flex; align-items: center; justify-content: center;"></i>
                                    </div>
                                </div>
                                <h6 class="mb-1">Interest Rate</h6>
                                <small class="text-muted text-center">Set default rate</small>
                                @php
                                    $currentRate = \App\Models\SystemSetting::where('key', 'default_interest_rate')->first();
                                @endphp
                                <div class="mt-2">
                                    <span class="badge bg-success">{{ $currentRate->value ?? '10' }}% per year</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- KYC Applications -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('admin.kyc.index') }}" class="text-decoration-none">
                            <div class="card quick-action-card border rounded p-4">
                                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                    <div class="d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                        <div class="avatar avatar-lg bg-label-primary" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                            <i class='bx bx-file-find' style="font-size: 2rem; display: flex; align-items: center; justify-content: center;"></i>
                                        </div>
                                    </div>
                                    <h6 class="mb-1">KYC Applications</h6>
                                    <small class="text-muted text-center">Manage applications</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Manage Users -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                            <div class="card quick-action-card border rounded p-4">
                                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                    <div class="d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                        <div class="avatar avatar-lg bg-label-warning" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                            <i class='bx bx-group' style="font-size: 2rem; display: flex; align-items: center; justify-content: center;"></i>
                                        </div>
                                    </div>
                                    <h6 class="mb-1">Manage Users</h6>
                                    <small class="text-muted text-center">User management</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Dashboard -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('dashboard') }}" class="text-decoration-none">
                            <div class="card quick-action-card border rounded p-4">
                                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                    <div class="d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                        <div class="avatar avatar-lg bg-label-info" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                            <i class='bx bx-bar-chart' style="font-size: 2rem; display: flex; align-items: center; justify-content: center;"></i>
                                        </div>
                                    </div>
                                    <h6 class="mb-1">Dashboard</h6>
                                    <small class="text-muted text-center">View statistics</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interest Rate Modal -->
<div class="modal fade" id="interestRateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class='bx bx-calculator me-2'></i>
                    Set Default Interest Rate
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.settings.interest-rate') }}" method="POST" id="interestRateForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class='bx bx-info-circle me-2'></i>
                        This interest rate will be applied to all new loan applications. Users can see this rate when filling the form.
                    </div>
                    
                    <div class="mb-3">
                        <label for="interest_rate" class="form-label">Annual Interest Rate (%)</label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control form-control-lg" 
                                   id="interest_rate" 
                                   name="interest_rate" 
                                   min="0" 
                                   max="100" 
                                   step="0.01" 
                                   value="{{ $currentRate->value ?? '10' }}" 
                                   required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Enter percentage (e.g., 10 for 10% per year)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="rate_type" class="form-label">Rate Type</label>
                        <select class="form-select" id="rate_type" name="rate_type">
                            <option value="fixed">Fixed Rate</option>
                            <option value="variable">Variable Rate</option>
                            <option value="reducing">Reducing Balance</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class='bx bx-check me-1'></i>
                        Save Interest Rate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.quick-action-card {
    height: 100%;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.quick-action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
}

.quick-action-card .avatar,
.quick-action-card .avatar-lg {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 64px !important;
    height: 64px !important;
}

.quick-action-card .avatar i,
.quick-action-card .avatar-lg i {
    font-size: 2rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    line-height: 1 !important;
}

.dashboard-row {
    display: flex;
    flex-wrap: wrap;
}

.dashboard-row > [class*='col-'] {
    display: flex;
}

.dashboard-row .card {
    width: 100%;
}

/* Header icon centering */
.card-body .d-flex .avatar,
.card-body .d-flex .avatar-xl {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.card-body .d-flex .avatar i,
.card-body .d-flex .avatar-xl i {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    line-height: 1 !important;
}
</style>
@endpush

@push('scripts')
<script>
function openInterestRateModal() {
    const modal = new bootstrap.Modal(document.getElementById('interestRateModal'));
    modal.show();
}

document.getElementById('interestRateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route('admin.settings.interest-rate') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('interestRateModal')).hide();
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
            alert.style.zIndex = '9999';
            alert.innerHTML = `
                <i class='bx bx-check-circle me-2'></i>
                Interest rate updated successfully to ${data.rate}%
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alert);
            
            // Reload page after 1.5 seconds
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>
@endpush
