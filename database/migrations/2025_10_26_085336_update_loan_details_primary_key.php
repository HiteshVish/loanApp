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
        // Drop the existing table and recreate with loan_id as primary key
        Schema::dropIfExists('loan_details');
        
        Schema::create('loan_details', function (Blueprint $table) {
            $table->string('loan_id', 20)->primary(); // loan_id as primary key
            $table->unsignedBigInteger('user_id');
            $table->decimal('loan_amount', 15, 2);
            $table->integer('tenure'); // in months
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the table
        Schema::dropIfExists('loan_details');
        
        // Recreate the original table structure
        Schema::create('loan_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('loan_amount', 15, 2);
            $table->integer('tenure'); // in months
            $table->string('loan_id', 20)->unique(); // Generated loan ID
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
