@extends('layouts.sneat')

@section('title', 'Create KYC Application')

@push('styles')
<style>
.option-card {
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid #e0e0e0;
}
.option-card:hover {
    border-color: #696cff;
    box-shadow: 0 4px 8px rgba(105, 108, 255, 0.2);
}
.option-card.selected {
    border-color: #696cff;
    background-color: #f0f0ff;
}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin / KYC /</span> Create New Application
    </h4>
    <a href="{{ route('admin.kyc.index') }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back"></i> Back
    </a>
</div>

<div class="alert alert-info">
    <i class="bx bx-info-circle"></i> <strong>Note:</strong> This form allows you to manually create a KYC application for a user.
</div>

<div class="row">
    <div class="col-md-12">
        <form action="{{ route('admin.kyc.store') }}" method="POST" enctype="multipart/form-data" id="kycCreateForm">
            @csrf

            <!-- Step 1: Choose Option -->
            <div class="card mb-4" id="optionSelection">
                <h5 class="card-header">Step 1: Choose Option</h5>
                <div class="card-body">
                    <div class="row">
                        <!-- Option 1: Create New User -->
                        <div class="col-md-6 mb-3">
                            <div class="option-card card h-100 text-center p-4" data-option="create-user">
                                <div class="mb-3">
                                    <i class="bx bx-user-plus bx-lg text-primary"></i>
                                </div>
                                <h5>Create Login for User</h5>
                                <p class="text-muted">Create a new user account with login credentials, then fill KYC application</p>
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="radio" name="creation_type" id="createUser" value="create_user">
                                    <label class="form-check-label ms-2" for="createUser">
                                        Select this option
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Option 2: Direct Fill -->
                        <div class="col-md-6 mb-3">
                            <div class="option-card card h-100 text-center p-4" data-option="direct-fill">
                                <div class="mb-3">
                                    <i class="bx bx-file-find bx-lg text-success"></i>
                                </div>
                                <h5>Direct Fill KYC Application</h5>
                                <p class="text-muted">Go directly to KYC form and fill the application</p>
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="radio" name="creation_type" id="directFill" value="direct_fill">
                                    <label class="form-check-label ms-2" for="directFill">
                                        Select this option
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-primary" id="continueBtn" disabled>
                            <i class="bx bx-right-arrow-alt"></i> Continue
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2A: Create User Form (Hidden by default) -->
            <div class="card mb-4" id="createUserSection" style="display: none;">
                <h5 class="card-header">Step 2: Create User Account</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">User Name <span class="text-danger">*</span></label>
                            <input type="text" name="new_user_name" class="form-control" placeholder="Enter full name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="new_user_email" class="form-control" placeholder="user@example.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_user_password" class="form-control" placeholder="Enter password">
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_user_password_confirmation" class="form-control" placeholder="Confirm password">
                        </div>
                    </div>
                    <input type="hidden" name="user_id" id="createdUserId">
                </div>
            </div>

            <!-- Navigation Buttons (for Create User option) -->
            <div class="card mb-4" id="navigationButtons" style="display: none;">
                <div class="card-body">
                    <button type="button" class="btn btn-outline-secondary" id="backBtn">
                        <i class="bx bx-left-arrow-alt"></i> Back
                    </button>
                    <button type="button" class="btn btn-primary" id="proceedToForm">
                        Proceed to KYC Form <i class="bx bx-right-arrow-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: KYC Form (Hidden by default) -->
            <div id="kycFormSection" style="display: none;">
                <div class="alert alert-success">
                    <i class="bx bx-check-circle"></i> <strong>Ready!</strong> Now fill the KYC application form below.
                </div>

                <!-- User Info section for direct fill (Hidden by default) -->
                <div class="card mb-4" id="directFillUserInfo" style="display: none;">
                    <h5 class="card-header">User Account Information</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">User Name <span class="text-danger">*</span></label>
                                <input type="text" name="direct_user_name" class="form-control" placeholder="Enter full name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="direct_user_email" class="form-control" placeholder="user@example.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="direct_user_password" class="form-control" placeholder="Enter password">
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="direct_user_password_confirmation" class="form-control" placeholder="Confirm password">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Details -->
                <div class="card mb-4">
                    <h5 class="card-header">Personal Details</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nationality <span class="text-danger">*</span></label>
                                <input type="text" name="nationality" class="form-control" value="{{ old('nationality', 'Indian') }}" required>
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
                                <input type="tel" name="mobile_number" class="form-control" placeholder="+91 1234567890" value="{{ old('mobile_number') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alternate Contact</label>
                                <input type="tel" name="alternate_contact" class="form-control" placeholder="+91 1234567890" value="{{ old('alternate_contact') }}">
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
                                <textarea name="current_address" class="form-control" rows="2" required>{{ old('current_address') }}</textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" name="current_city" class="form-control" value="{{ old('current_city') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">State <span class="text-danger">*</span></label>
                                <input type="text" name="current_state" class="form-control" value="{{ old('current_state') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ZIP Code <span class="text-danger">*</span></label>
                                <input type="text" name="current_zip_code" class="form-control" value="{{ old('current_zip_code') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Residential Status <span class="text-danger">*</span></label>
                                <select name="residential_status" class="form-select" required>
                                    <option value="">Select Status</option>
                                    <option value="Own">Own</option>
                                    <option value="Rent">Rent</option>
                                    <option value="Family">Family</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Years at Current Address <span class="text-danger">*</span></label>
                                <input type="number" name="years_at_current_address" class="form-control" value="{{ old('years_at_current_address') }}" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="address_same_as_current" id="sameAddressAdmin" value="1">
                                    <label class="form-check-label same-address-toggle" for="sameAddressAdmin" style="cursor: pointer; user-select: none;">
                                        Permanent address is same as current address
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div id="permanentAddressSection">
                            <h6 class="text-primary mb-3 mt-3">Permanent Address</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Street Address</label>
                                    <textarea name="permanent_address" class="form-control" rows="2">{{ old('permanent_address') }}</textarea>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="permanent_city" class="form-control" value="{{ old('permanent_city') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" name="permanent_state" class="form-control" value="{{ old('permanent_state') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">ZIP Code</label>
                                    <input type="text" name="permanent_zip_code" class="form-control" value="{{ old('permanent_zip_code') }}">
                                </div>
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
                                    <option value="Salaried">Salaried</option>
                                    <option value="Self-employed">Self-employed</option>
                                    <option value="Student">Student</option>
                                    <option value="Retired">Retired</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Employer Name</label>
                                <input type="text" name="employer_name" class="form-control" value="{{ old('employer_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Designation/Occupation</label>
                                <input type="text" name="designation" class="form-control" value="{{ old('designation') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Employment Tenure (Months)</label>
                                <input type="number" name="employment_tenure_months" class="form-control" placeholder="12" value="{{ old('employment_tenure_months') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Monthly Income (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="monthly_income" class="form-control" placeholder="50000" value="{{ old('monthly_income') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Other Income (₹)</label>
                                <input type="number" step="0.01" name="other_income" class="form-control" placeholder="0" value="{{ old('other_income') }}">
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
                                <input type="number" step="0.01" name="loan_amount" class="form-control" placeholder="100000" value="{{ old('loan_amount') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loan Tenure (Months) <span class="text-danger">*</span></label>
                                <input type="number" name="loan_tenure_months" class="form-control" placeholder="12" value="{{ old('loan_tenure_months') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loan Purpose <span class="text-danger">*</span></label>
                                <input type="text" name="loan_purpose" class="form-control" value="{{ old('loan_purpose') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Interest Rate (% per annum)</label>
                                @php
                                    $defaultRate = \App\Models\SystemSetting::get('default_interest_rate', 10);
                                @endphp
                                <input type="text" class="form-control" value="{{ $defaultRate }}% per year" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                                <input type="hidden" name="interest_rate" value="{{ $defaultRate }}">
                                <small class="text-muted">Rate set by admin</small>
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
                                <label class="form-label">Aadhar Number <span class="text-danger">*</span></label>
                                <input type="text" name="aadhar_number" class="form-control" pattern="[0-9]{12}" maxlength="12" value="{{ old('aadhar_number') }}" required>
                                <small class="text-muted">12 digit Aadhar number</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">PAN Number <span class="text-danger">*</span></label>
                                <input type="text" name="pan_number" class="form-control" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" maxlength="10" value="{{ old('pan_number') }}" required>
                                <small class="text-muted">10 digit PAN number (e.g., ABCDE1234F)</small>
                            </div>
                        </div>
                        <hr>
                        <h6 class="text-primary mb-3">Upload Documents</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Photograph</label>
                                <input type="file" name="photograph" class="form-control file-input" accept="image/*" data-max-size="2048">
                                <small class="text-muted">JPG, PNG <span class="text-danger">(Max: 2MB)</span></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Aadhar Card</label>
                                <input type="file" name="aadhar_card" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png" data-max-size="2048">
                                <small class="text-muted">PDF, JPG, PNG <span class="text-danger">(Max: 2MB)</span></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload PAN Card</label>
                                <input type="file" name="pan_card" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png" data-max-size="2048">
                                <small class="text-muted">PDF, JPG, PNG <span class="text-danger">(Max: 2MB)</span></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Address Proof</label>
                                <input type="file" name="address_proof" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png" data-max-size="2048">
                                <small class="text-muted">PDF, JPG, PNG <span class="text-danger">(Max: 2MB)</span></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Status (Admin) -->
                <div class="card mb-4">
                    <h5 class="card-header">Application Status</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="pending" selected>Pending</option>
                                    <option value="under_review">Under Review</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                <small class="text-muted">Default: Pending (for approval)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Admin Notes</label>
                                <textarea name="admin_notes" class="form-control" rows="3" placeholder="Any notes about this application...">{{ old('admin_notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mb-4">
                    <button type="button" class="btn btn-outline-secondary" id="backFromForm">
                        <i class="bx bx-left-arrow-alt"></i> Back
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check"></i> Create KYC Application
                    </button>
                    <a href="{{ route('admin.kyc.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const optionCards = document.querySelectorAll('.option-card');
    const continueBtn = document.getElementById('continueBtn');
    const createUserRadio = document.getElementById('createUser');
    const directFillRadio = document.getElementById('directFill');
    const optionSelection = document.getElementById('optionSelection');
    const createUserSection = document.getElementById('createUserSection');
    const selectUserSection = document.getElementById('selectUserSection');
    const navigationButtons = document.getElementById('navigationButtons');
    const kycFormSection = document.getElementById('kycFormSection');
    const backBtn = document.getElementById('backBtn');
    const proceedToForm = document.getElementById('proceedToForm');

    // Handle option card clicks
    optionCards.forEach(card => {
        card.addEventListener('click', function() {
            const option = this.getAttribute('data-option');
            
            // Remove selected class from all cards
            optionCards.forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            this.classList.add('selected');
            
            // Check corresponding radio button
            if (option === 'create-user') {
                createUserRadio.checked = true;
            } else {
                directFillRadio.checked = true;
            }
            
            // Enable continue button
            continueBtn.disabled = false;
        });
    });

    // Handle radio button changes
    document.querySelectorAll('input[name="creation_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            continueBtn.disabled = false;
            
            // Update card selection
            optionCards.forEach(card => {
                card.classList.remove('selected');
                if (card.getAttribute('data-option') === (this.value === 'create_user' ? 'create-user' : 'direct-fill')) {
                    card.classList.add('selected');
                }
            });
        });
    });

    // Handle continue button click
    continueBtn.addEventListener('click', function() {
        const selectedOption = document.querySelector('input[name="creation_type"]:checked').value;
        
        // Hide option selection
        optionSelection.style.display = 'none';
        
        // Show appropriate section
        if (selectedOption === 'create_user') {
            // Show user creation form
            createUserSection.style.display = 'block';
            navigationButtons.style.display = 'block';
            // Make fields required
            document.querySelector('input[name="new_user_name"]').required = true;
            document.querySelector('input[name="new_user_email"]').required = true;
            document.querySelector('input[name="new_user_password"]').required = true;
            document.querySelector('input[name="new_user_password_confirmation"]').required = true;
        } else {
            // Direct fill - go straight to KYC form with user info section
            kycFormSection.style.display = 'block';
            document.getElementById('directFillUserInfo').style.display = 'block';
            // Make direct fill user fields required
            document.querySelector('input[name="direct_user_name"]').required = true;
            document.querySelector('input[name="direct_user_email"]').required = true;
            document.querySelector('input[name="direct_user_password"]').required = true;
            document.querySelector('input[name="direct_user_password_confirmation"]').required = true;
            kycFormSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    // Handle back button
    backBtn.addEventListener('click', function() {
        // Show option selection
        optionSelection.style.display = 'block';
        
        // Hide other sections
        createUserSection.style.display = 'none';
        navigationButtons.style.display = 'none';
        kycFormSection.style.display = 'none';
        document.getElementById('directFillUserInfo').style.display = 'none';
        
        // Remove required attributes
        document.querySelector('input[name="new_user_name"]').required = false;
        document.querySelector('input[name="new_user_email"]').required = false;
        document.querySelector('input[name="new_user_password"]').required = false;
        document.querySelector('input[name="new_user_password_confirmation"]').required = false;
        document.querySelector('input[name="direct_user_name"]').required = false;
        document.querySelector('input[name="direct_user_email"]').required = false;
        document.querySelector('input[name="direct_user_password"]').required = false;
        document.querySelector('input[name="direct_user_password_confirmation"]').required = false;
    });

    // Handle proceed to form button (only for create_user option)
    proceedToForm.addEventListener('click', function() {
        // Validate user creation fields
        const name = document.querySelector('input[name="new_user_name"]').value;
        const email = document.querySelector('input[name="new_user_email"]').value;
        const password = document.querySelector('input[name="new_user_password"]').value;
        const confirmPassword = document.querySelector('input[name="new_user_password_confirmation"]').value;
        
        if (!name || !email || !password || !confirmPassword) {
            alert('Please fill all user creation fields!');
            return;
        }
        
        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }
        
        if (password.length < 8) {
            alert('Password must be at least 8 characters!');
            return;
        }
        
        // Hide previous sections
        createUserSection.style.display = 'none';
        navigationButtons.style.display = 'none';
        
        // Show KYC form
        kycFormSection.style.display = 'block';
        
        // Scroll to KYC form
        kycFormSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // Handle back from KYC form
    const backFromFormBtn = document.getElementById('backFromForm');
    if (backFromFormBtn) {
        backFromFormBtn.addEventListener('click', function() {
            const selectedOption = document.querySelector('input[name="creation_type"]:checked').value;
            
            // Hide KYC form
            kycFormSection.style.display = 'none';
            document.getElementById('directFillUserInfo').style.display = 'none';
            
            // Show previous step based on option
            if (selectedOption === 'create_user') {
                createUserSection.style.display = 'block';
                navigationButtons.style.display = 'block';
            } else {
                // Direct fill - go back to option selection
                optionSelection.style.display = 'block';
            }
        });
    }

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
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            }
        });
    });

    // Same address checkbox functionality for admin form
    const sameAddressCheckbox = document.getElementById('sameAddressAdmin');
    const permanentAddressSection = document.getElementById('permanentAddressSection');
    
    if (sameAddressCheckbox && permanentAddressSection) {
        sameAddressCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Copy current address to permanent address
                document.querySelector('[name="permanent_address"]').value = document.querySelector('[name="current_address"]').value;
                document.querySelector('[name="permanent_city"]').value = document.querySelector('[name="current_city"]').value;
                document.querySelector('[name="permanent_state"]').value = document.querySelector('[name="current_state"]').value;
                document.querySelector('[name="permanent_zip_code"]').value = document.querySelector('[name="current_zip_code"]').value;
                
                // Hide permanent address section
                permanentAddressSection.style.display = 'none';
            } else {
                // Clear permanent address fields
                document.querySelector('[name="permanent_address"]').value = '';
                document.querySelector('[name="permanent_city"]').value = '';
                document.querySelector('[name="permanent_state"]').value = '';
                document.querySelector('[name="permanent_zip_code"]').value = '';
                
                // Show permanent address section
                permanentAddressSection.style.display = 'block';
            }
        });
    }
});
</script>
@endpush
@endsection

