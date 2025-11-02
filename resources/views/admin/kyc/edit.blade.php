@extends('layouts.sneat')

@section('title', 'Edit KYC Application')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin / KYC /</span> Edit Application #{{ $application->id }}
    </h4>
    <a href="{{ route('admin.kyc.show', $application) }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-md-12">
        <form action="{{ route('admin.kyc.update', $application) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- User Info Alert -->
            <div class="alert alert-info mb-4">
                <strong>Editing KYC for:</strong> {{ $application->user->name }} ({{ $application->user->email }})
            </div>

            <!-- Personal Details -->
            <div class="card mb-4">
                <h5 class="card-header">Personal Details</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $application->full_name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $application->date_of_birth->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender', $application->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $application->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $application->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nationality <span class="text-danger">*</span></label>
                            <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $application->nationality) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-4">
                <h5 class="card-header">Contact Information</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="tel" name="mobile_number" class="form-control" value="{{ old('mobile_number', $application->mobile_number) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $application->email) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alternate Contact</label>
                            <input type="tel" name="alternate_contact" class="form-control" value="{{ old('alternate_contact', $application->alternate_contact) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Details -->
            <div class="card mb-4">
                <h5 class="card-header">Address Details</h5>
                <div class="card-body">
                    <h6 class="text-primary mb-3">Current Address</h6>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Street Address <span class="text-danger">*</span></label>
                            <textarea name="current_address" class="form-control" rows="2" required>{{ old('current_address', $application->current_address) }}</textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" name="current_city" class="form-control" value="{{ old('current_city', $application->current_city) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <input type="text" name="current_state" class="form-control" value="{{ old('current_state', $application->current_state) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ZIP Code <span class="text-danger">*</span></label>
                            <input type="text" name="current_zip_code" class="form-control" value="{{ old('current_zip_code', $application->current_zip_code) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Residential Status <span class="text-danger">*</span></label>
                            <select name="residential_status" class="form-select" required>
                                <option value="">Select Status</option>
                                <option value="Own" {{ old('residential_status', $application->residential_status) == 'Own' ? 'selected' : '' }}>Own</option>
                                <option value="Rent" {{ old('residential_status', $application->residential_status) == 'Rent' ? 'selected' : '' }}>Rent</option>
                                <option value="Family" {{ old('residential_status', $application->residential_status) == 'Family' ? 'selected' : '' }}>Family</option>
                                <option value="Other" {{ old('residential_status', $application->residential_status) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Years at Current Address <span class="text-danger">*</span></label>
                            <input type="number" name="years_at_current_address" class="form-control" value="{{ old('years_at_current_address', $application->years_at_current_address) }}" required>
                        </div>
                    </div>
                    
                    <h6 class="text-primary mb-3 mt-3">Permanent Address</h6>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Street Address</label>
                            <textarea name="permanent_address" class="form-control" rows="2">{{ old('permanent_address', $application->permanent_address) }}</textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" name="permanent_city" class="form-control" value="{{ old('permanent_city', $application->permanent_city) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">State</label>
                            <input type="text" name="permanent_state" class="form-control" value="{{ old('permanent_state', $application->permanent_state) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ZIP Code</label>
                            <input type="text" name="permanent_zip_code" class="form-control" value="{{ old('permanent_zip_code', $application->permanent_zip_code) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employment & Income -->
            <div class="card mb-4">
                <h5 class="card-header">Employment & Income Details</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                            <select name="employment_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="Salaried" {{ old('employment_type', $application->employment_type) == 'Salaried' ? 'selected' : '' }}>Salaried</option>
                                <option value="Self-employed" {{ old('employment_type', $application->employment_type) == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                                <option value="Student" {{ old('employment_type', $application->employment_type) == 'Student' ? 'selected' : '' }}>Student</option>
                                <option value="Retired" {{ old('employment_type', $application->employment_type) == 'Retired' ? 'selected' : '' }}>Retired</option>
                                <option value="Other" {{ old('employment_type', $application->employment_type) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employer Name</label>
                            <input type="text" name="employer_name" class="form-control" value="{{ old('employer_name', $application->employer_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Designation/Occupation</label>
                            <input type="text" name="designation" class="form-control" value="{{ old('designation', $application->designation) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employment Tenure (Months) <span class="text-danger">*</span></label>
                            <input type="number" name="employment_tenure_months" class="form-control" value="{{ old('employment_tenure_months', $application->employment_tenure_months) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Monthly Income (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="monthly_income" class="form-control" value="{{ old('monthly_income', $application->monthly_income) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Other Income (₹)</label>
                            <input type="number" step="0.01" name="other_income" class="form-control" value="{{ old('other_income', $application->other_income) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loan Details -->
            <div class="card mb-4">
                <h5 class="card-header">Loan Details</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Loan Amount Requested (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="loan_amount" class="form-control" value="{{ old('loan_amount', $application->loan_amount) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Loan Tenure (Months) <span class="text-danger">*</span></label>
                            <input type="number" name="loan_tenure_months" class="form-control" value="{{ old('loan_tenure_months', $application->loan_tenure_months) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Loan Purpose <span class="text-danger">*</span></label>
                            <input type="text" name="loan_purpose" class="form-control" value="{{ old('loan_purpose', $application->loan_purpose) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Interest Rate (% per annum)</label>
                            <input type="number" step="0.01" name="interest_rate" class="form-control" value="{{ old('interest_rate', $application->interest_rate) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- KYC Documents -->
            <div class="card mb-4">
                <h5 class="card-header">KYC Documentation</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Aadhar Card Number <span class="text-danger">*</span></label>
                            <input type="text" name="aadhar_number" class="form-control" maxlength="12" value="{{ old('aadhar_number', $application->aadhar_number) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">PAN Card Number <span class="text-danger">*</span></label>
                            <input type="text" name="pan_number" class="form-control" maxlength="10" value="{{ old('pan_number', $application->pan_number) }}" required style="text-transform: uppercase;">
                        </div>
                    </div>
                    
                    <h6 class="mt-3 mb-3">Update Documents (Optional - Leave blank to keep existing)</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload Photograph</label>
                            @if($application->photograph_path)
                                <div class="mb-2">
                                    <small class="text-muted">Current: <a href="{{ asset('storage/' . $application->photograph_path) }}" target="_blank">View</a></small>
                                </div>
                            @endif
                            <input type="file" name="photograph" class="form-control file-input" accept="image/*" data-max-size="2048">
                            <small class="text-muted">JPG, PNG <span class="text-danger">(Max: 2MB)</span></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload Address Proof</label>
                            @if($application->address_proof_path)
                                <div class="mb-2">
                                    <small class="text-muted">Current: <a href="{{ asset('storage/' . $application->address_proof_path) }}" target="_blank">View</a></small>
                                </div>
                            @endif
                            <input type="file" name="address_proof" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png" data-max-size="2048">
                            <small class="text-muted">PDF, JPG, PNG <span class="text-danger">(Max: 2MB)</span></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload Aadhar Card</label>
                            @if($application->aadhar_card_path)
                                <div class="mb-2">
                                    <small class="text-muted">Current: <a href="{{ asset('storage/' . $application->aadhar_card_path) }}" target="_blank">View</a></small>
                                </div>
                            @endif
                            <input type="file" name="aadhar_card" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png" data-max-size="2048">
                            <small class="text-muted">PDF, JPG, PNG <span class="text-danger">(Max: 2MB)</span></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload PAN Card</label>
                            @if($application->pan_card_path)
                                <div class="mb-2">
                                    <small class="text-muted">Current: <a href="{{ asset('storage/' . $application->pan_card_path) }}" target="_blank">View</a></small>
                                </div>
                            @endif
                            <input type="file" name="pan_card" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png" data-max-size="2048">
                            <small class="text-muted">PDF, JPG, PNG <span class="text-danger">(Max: 2MB)</span></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Status -->
            <div class="card mb-4">
                <h5 class="card-header">Application Status</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ old('status', $application->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="under_review" {{ old('status', $application->status) == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                <option value="approved" {{ old('status', $application->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ old('status', $application->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Admin Notes</label>
                            <textarea name="admin_notes" class="form-control" rows="3">{{ old('admin_notes', $application->admin_notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save"></i> Update Application
                </button>
                <a href="{{ route('admin.kyc.show', $application) }}" class="btn btn-outline-secondary">
                    <i class="bx bx-x"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// File size validation
document.querySelectorAll('.file-input').forEach(input => {
    input.addEventListener('change', function() {
        const maxSize = parseInt(this.getAttribute('data-max-size')) * 1024; // Convert KB to bytes
        
        if (this.files.length > 0) {
            const file = this.files[0];
            const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
            
            if (file.size > maxSize) {
                alert(`File "${file.name}" is too large! Maximum size is 2MB. Your file is ${fileSizeMB}MB.`);
                this.value = '';
            }
        }
    });
});
</script>
@endpush
@endsection

