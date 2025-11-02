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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            
            // Personal Details
            $table->string('name');
            $table->date('dob');
            $table->string('gender');
            $table->string('nationality');
            
            // Contact Info
            $table->string('mobile');
            $table->string('email');
            $table->text('current_address');
            $table->text('permanent_address');
            
            // Documents
            $table->string('aadhar')->nullable();
            $table->string('pan')->nullable();
            $table->string('photo')->nullable();
            
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
        Schema::dropIfExists('user_details');
    }
};
