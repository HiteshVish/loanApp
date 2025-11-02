@extends('layouts.kyc-minimal')

@section('title', 'KYC Application')

@push('styles')
<style>
.kyc-step {
    display: none;
}
.kyc-step.active {
    display: block;
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
}
.step-item {
    flex: 1;
    text-align: center;
    position: relative;
}
.step-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 50%;
    width: 100%;
    height: 2px;
    background: #e0e0e0;
    z-index: -1;
}
.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e0e0e0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 8px;
    transition: all 0.3s;
}
.step-item.active .step-circle {
    background: #696cff;
    color: white;
}
.step-item.completed .step-circle {
    background: #71dd37;
    color: white;
}
.same-address-toggle {
    cursor: pointer;
    user-select: none;
}
.is-invalid {
    border-color: #dc3545 !important;
}
.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card-header border-bottom mb-4 pb-3">
            <h4 class="mb-2">🏦 Loan KYC Application</h4>
            <p class="text-muted mb-0">Please fill all required information to process your loan application</p>
        </div>
        <div class="card-body px-4">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step-item active" data-step="1">
                        <div class="step-circle">1</div>
                        <small>Personal</small>
                    </div>
                    <div class="step-item" data-step="2">
                        <div class="step-circle">2</div>
                        <small>Contact</small>
                    </div>
                    <div class="step-item" data-step="3">
                        <div class="step-circle">3</div>
                        <small>Address</small>
                    </div>
                    <div class="step-item" data-step="4">
                        <div class="step-circle">4</div>
                        <small>Employment</small>
                    </div>
                    <div class="step-item" data-step="5">
                        <div class="step-circle">5</div>
                        <small>Loan</small>
                    </div>
                    <div class="step-item" data-step="6">
                        <div class="step-circle">6</div>
                        <small>Documents</small>
                    </div>
                </div>

                <form action="{{ route('kyc.store') }}" method="POST" enctype="multipart/form-data" id="kycForm">
                    @csrf

                    <!-- Step 1: Personal Details -->
                    <div class="kyc-step active" data-step="1">
                        <h5 class="mb-4">Personal Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', Auth::user()->name) }}" required>
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

                    <!-- Step 2: Contact Information -->
                    <div class="kyc-step" data-step="2">
                        <h5 class="mb-4">Contact Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="tel" name="mobile_number" class="form-control" placeholder="+91 1234567890" value="{{ old('mobile_number') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email ID <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', Auth::user()->email) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alternate Contact Number</label>
                                <input type="tel" name="alternate_contact" class="form-control" placeholder="+91 1234567890" value="{{ old('alternate_contact') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Address Details -->
                    <div class="kyc-step" data-step="3">
                        <h5 class="mb-4">Address Details</h5>
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
                                    <input class="form-check-input" type="checkbox" name="address_same_as_current" id="sameAddress" value="1">
                                    <label class="form-check-label same-address-toggle" for="sameAddress">
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

                    <!-- Step 4: Employment Details -->
                    <div class="kyc-step" data-step="4">
                        <h5 class="mb-4">Employment & Income Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select name="employment_type" class="form-select" id="employmentType" required>
                                    <option value="">Select Type</option>
                                    <option value="Salaried">Salaried</option>
                                    <option value="Self-employed">Self-employed</option>
                                    <option value="Student">Student</option>
                                    <option value="Retired">Retired</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="employerField" style="display: none;">
                                <label class="form-label">Employer Name</label>
                                <input type="text" name="employer_name" class="form-control" value="{{ old('employer_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Designation/Occupation</label>
                                <input type="text" name="designation" class="form-control" value="{{ old('designation') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Monthly Income (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="monthly_income" class="form-control" placeholder="50000" value="{{ old('monthly_income') }}" required>
                                <small class="text-muted">Net monthly income</small>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Loan Details -->
                    <div class="kyc-step" data-step="5">
                        <h5 class="mb-4">Loan Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loan Amount Requested (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="loan_amount" id="loanAmount" class="form-control" placeholder="100000" value="{{ old('loan_amount') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loan Tenure (Months) <span class="text-danger">*</span></label>
                                <input type="number" name="loan_tenure_months" id="loanTenure" class="form-control" placeholder="12" value="{{ old('loan_tenure_months') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loan Purpose <span class="text-danger">*</span></label>
                                <select name="loan_purpose" class="form-select" required>
                                    <option value="">Select Purpose</option>
                                    <option value="Home">Home</option>
                                    <option value="Education">Education</option>
                                    <option value="Personal">Personal</option>
                                    <option value="Business">Business</option>
                                    <option value="Medical">Medical</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Interest Rate (% per annum)</label>
                                @php
                                    $defaultRate = \App\Models\SystemSetting::get('default_interest_rate', 10);
                                @endphp
                                <input type="text" class="form-control" value="{{ $defaultRate }}% per year" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                                <input type="hidden" name="interest_rate" id="interestRate" value="{{ $defaultRate }}">
                                <small class="text-muted">Rate set by admin</small>
                            </div>
                            <div class="col-md-12">
                                <div class="alert alert-primary">
                                    <h6 class="alert-heading">Estimated EMI</h6>
                                    <p class="mb-0" id="emiDisplay">Fill loan details to calculate EMI</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6: KYC Documents -->
                    <div class="kyc-step" data-step="6">
                        <h5 class="mb-4">KYC Documentation</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Photograph</label>
                                <input type="file" name="photograph" class="form-control file-input" accept="image/*" data-max-size="2048">
                                <small class="text-muted">JPG, PNG <span class="text-danger">(Max: 2MB)</span></small>
                                <div class="file-size-info mt-1" style="display: none;">
                                    <small class="text-success"><i class="bx bx-check-circle"></i> <span class="file-name"></span> (<span class="file-size"></span>)</small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Aadhar Card</label>
                                <input type="file" name="aadhar_card" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png" data-max-size="2048">
                                <small class="text-muted">Aadhar card copy <span class="text-danger">(Max: 2MB)</span></small>
                                <div class="file-size-info mt-1" style="display: none;">
                                    <small class="text-success"><i class="bx bx-check-circle"></i> <span class="file-name"></span> (<span class="file-size"></span>)</small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload PAN Card</label>
                                <input type="file" name="pan_card" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png" data-max-size="2048">
                                <small class="text-muted">PAN card copy <span class="text-danger">(Max: 2MB)</span></small>
                                <div class="file-size-info mt-1" style="display: none;">
                                    <small class="text-success"><i class="bx bx-check-circle"></i> <span class="file-name"></span> (<span class="file-size"></span>)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                            <i class="bx bx-chevron-left"></i> Previous
                        </button>
                        <div></div>
                        <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                            Next <i class="bx bx-chevron-right"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                            <i class="bx bx-check-circle"></i> Submit Application
                        </button>
                    </div>
                </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 6;

function showStep(step) {
    document.querySelectorAll('.kyc-step').forEach(el => el.classList.remove('active'));
    document.querySelector(`.kyc-step[data-step="${step}"]`).classList.add('active');
    
    document.querySelectorAll('.step-item').forEach((el, index) => {
        el.classList.remove('active', 'completed');
        if (index + 1 < step) el.classList.add('completed');
        if (index + 1 === step) el.classList.add('active');
    });
    
    document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-block';
    document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'inline-block';
    document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-block' : 'none';
}

function validateCurrentStep() {
    const currentStepDiv = document.querySelector(`.kyc-step[data-step="${currentStep}"]`);
    const requiredFields = currentStepDiv.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    // Remove all previous error messages and invalid classes
    currentStepDiv.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    currentStepDiv.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    
    requiredFields.forEach(field => {
        let fieldIsValid = true;
        
        // Check if field is empty
        if (!field.value.trim()) {
            fieldIsValid = false;
        }
        
        // Additional validation for specific field types
        if (field.type === 'email' && field.value.trim()) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(field.value)) {
                fieldIsValid = false;
            }
        }
        
        if (field.type === 'tel' && field.value.trim()) {
            // Basic phone validation (at least 10 digits)
            const phonePattern = /\d{10}/;
            if (!phonePattern.test(field.value.replace(/\D/g, ''))) {
                fieldIsValid = false;
            }
        }
        
        if (field.type === 'date' && field.value.trim()) {
            // Check if date is valid and not in future
            const selectedDate = new Date(field.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (selectedDate >= today) {
                fieldIsValid = false;
            }
        }
        
        if (!fieldIsValid) {
            field.classList.add('is-invalid');
            
            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = 'This field is required and must be valid';
            field.parentNode.appendChild(errorDiv);
            
            isValid = false;
        }
    });
    
    if (!isValid) {
        // Scroll to first invalid field
        const firstInvalid = currentStepDiv.querySelector('.is-invalid');
        if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus();
        }
    }
    
    return isValid;
}

function changeStep(direction) {
    // If moving forward, validate current step
    if (direction > 0) {
        if (!validateCurrentStep()) {
            return; // Don't proceed if validation fails
        }
    }
    
    const newStep = currentStep + direction;
    if (newStep >= 1 && newStep <= totalSteps) {
        currentStep = newStep;
        showStep(currentStep);
    }
}

// Same address checkbox
document.getElementById('sameAddress')?.addEventListener('change', function() {
    const section = document.getElementById('permanentAddressSection');
    if (this.checked) {
        section.style.display = 'none';
        section.querySelectorAll('input, textarea').forEach(el => el.removeAttribute('required'));
    } else {
        section.style.display = 'block';
    }
});

// Employment type change
document.getElementById('employmentType')?.addEventListener('change', function() {
    const employerField = document.getElementById('employerField');
    if (['Salaried', 'Self-employed'].includes(this.value)) {
        employerField.querySelector('input').setAttribute('required', 'required');
    } else {
        employerField.querySelector('input').removeAttribute('required');
    }
});

// EMI Calculator
function calculateEMI() {
    const amount = parseFloat(document.getElementById('loanAmount')?.value || 0);
    const tenure = parseInt(document.getElementById('loanTenure')?.value || 0);
    const rate = parseFloat(document.getElementById('interestRate')?.value || 10);
    
    if (amount && tenure) {
        const monthlyRate = rate / 12 / 100;
        let emi;
        if (monthlyRate === 0) {
            emi = amount / tenure;
        } else {
            emi = amount * monthlyRate * Math.pow(1 + monthlyRate, tenure) / (Math.pow(1 + monthlyRate, tenure) - 1);
        }
        document.getElementById('emiDisplay').innerHTML = `
            <strong>₹${emi.toFixed(2)}/month</strong><br>
            <small>Total Payment: ₹${(emi * tenure).toFixed(2)} | Interest: ₹${((emi * tenure) - amount).toFixed(2)}</small>
        `;
    }
}

['loanAmount', 'loanTenure', 'interestRate'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', calculateEMI);
});

// Add real-time validation clearing when user starts typing
document.querySelectorAll('input, select, textarea').forEach(field => {
    field.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
            this.classList.remove('is-invalid');
            const errorMsg = this.parentNode.querySelector('.invalid-feedback');
            if (errorMsg) {
                errorMsg.remove();
            }
        }
    });
    
    field.addEventListener('change', function() {
        if (this.classList.contains('is-invalid')) {
            this.classList.remove('is-invalid');
            const errorMsg = this.parentNode.querySelector('.invalid-feedback');
            if (errorMsg) {
                errorMsg.remove();
            }
        }
    });
});

// Validate form before submission
document.getElementById('kycForm').addEventListener('submit', function(e) {
    if (!validateCurrentStep()) {
        e.preventDefault();
        return false;
    }
    
    // Validate file sizes before submission
    const fileInputs = document.querySelectorAll('.file-input');
    let hasOversizedFile = false;
    
    fileInputs.forEach(input => {
        if (input.files.length > 0) {
            const file = input.files[0];
            const maxSize = parseInt(input.getAttribute('data-max-size')) * 1024; // Convert KB to bytes
            
            if (file.size > maxSize) {
                hasOversizedFile = true;
                input.classList.add('is-invalid');
                alert(`File "${file.name}" is too large. Maximum size is ${input.getAttribute('data-max-size')}KB (${(maxSize/1024/1024).toFixed(2)}MB)`);
            }
        }
    });
    
    if (hasOversizedFile) {
        e.preventDefault();
        return false;
    }
});

// File size validation
document.querySelectorAll('.file-input').forEach(input => {
    input.addEventListener('change', function() {
        const fileInfoDiv = this.parentElement.querySelector('.file-size-info');
        const maxSize = parseInt(this.getAttribute('data-max-size')) * 1024; // Convert KB to bytes
        
        if (this.files.length > 0) {
            const file = this.files[0];
            const fileSizeKB = (file.size / 1024).toFixed(2);
            const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
            
            // Check if file exceeds max size
            if (file.size > maxSize) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                
                // Show error
                const existingError = this.parentElement.querySelector('.invalid-feedback');
                if (existingError) {
                    existingError.remove();
                }
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.style.display = 'block';
                errorDiv.innerHTML = `<i class="bx bx-error-circle"></i> File too large! Maximum size is ${(maxSize/1024/1024).toFixed(2)}MB. Your file is ${fileSizeMB}MB.`;
                this.parentElement.appendChild(errorDiv);
                
                fileInfoDiv.style.display = 'none';
                
                // Clear the file input
                this.value = '';
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                
                // Remove any error messages
                const existingError = this.parentElement.querySelector('.invalid-feedback');
                if (existingError) {
                    existingError.remove();
                }
                
                // Show file info
                fileInfoDiv.querySelector('.file-name').textContent = file.name;
                fileInfoDiv.querySelector('.file-size').textContent = fileSizeMB >= 1 ? `${fileSizeMB} MB` : `${fileSizeKB} KB`;
                fileInfoDiv.style.display = 'block';
            }
        } else {
            this.classList.remove('is-invalid', 'is-valid');
            fileInfoDiv.style.display = 'none';
        }
    });
});

showStep(1);
</script>
@endpush
