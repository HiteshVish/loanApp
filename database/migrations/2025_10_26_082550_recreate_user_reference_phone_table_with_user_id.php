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
        // Drop the existing table
        Schema::dropIfExists('user_reference_phone');
        
        // Recreate the table with correct structure
        Schema::create('user_reference_phone', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('contact_number');
            $table->string('name')->nullable();
            $table->timestamps();
            
            // Add foreign key constraint to users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the table
        Schema::dropIfExists('user_reference_phone');
        
        // Recreate the original table structure
        Schema::create('user_reference_phone', function (Blueprint $table) {
            $table->id();
            $table->string('loan_id', 20);
            $table->string('contact_number');
            $table->string('name')->nullable();
            $table->timestamps();
            
            // Add foreign key constraint to kyc_applications table
            $table->foreign('loan_id')->references('loan_id')->on('kyc_applications')->onDelete('cascade');
        });
    }
};
