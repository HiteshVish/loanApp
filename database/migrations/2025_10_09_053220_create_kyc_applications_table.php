<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kyc_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Personal Details
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->string('gender')->nullable();
            $table->string('nationality');
            
            // Contact Information
            $table->string('mobile_number');
            $table->string('email');
            $table->string('alternate_contact')->nullable();
            
            // Address Details
            $table->text('current_address');
            $table->string('current_city');
            $table->string('current_state');
            $table->string('current_zip_code');
            $table->text('permanent_address')->nullable();
            $table->string('permanent_city')->nullable();
            $table->string('permanent_state')->nullable();
            $table->string('permanent_zip_code')->nullable();
            $table->boolean('address_same_as_current')->default(false);
            $table->string('residential_status'); // Own, Rent, Family, Other
            $table->integer('years_at_current_address');
            
            // Employment and Income Details
            $table->string('employment_type'); // Salaried, Self-employed, Student, Retired, Other
            $table->string('employer_name')->nullable();
            $table->string('designation')->nullable();
            $table->decimal('monthly_income', 15, 2);
            $table->decimal('other_income', 15, 2)->nullable();
            $table->integer('employment_tenure_months');
            
            // Loan Details
            $table->decimal('loan_amount', 15, 2);
            $table->integer('loan_tenure_months');
            $table->string('loan_purpose'); // Home, Education, Personal, Business, etc.
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->decimal('estimated_emi', 15, 2)->nullable();
            
            // KYC Documentation
            $table->string('aadhar_number');
            $table->string('pan_number');
            $table->string('photograph_path')->nullable();
            $table->string('address_proof_path')->nullable();
            $table->string('aadhar_card_path')->nullable();
            $table->string('pan_card_path')->nullable();
            
            // Status and Admin Review
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_applications');
    }
};
