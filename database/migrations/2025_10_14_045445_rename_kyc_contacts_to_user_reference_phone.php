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
        // Rename table
        Schema::rename('kyc_contacts', 'user_reference_phone');
        
        // Rename column app_id to loan_id
        Schema::table('user_reference_phone', function (Blueprint $table) {
            $table->renameColumn('app_id', 'loan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename column back
        Schema::table('user_reference_phone', function (Blueprint $table) {
            $table->renameColumn('loan_id', 'app_id');
        });
        
        // Rename table back
        Schema::rename('user_reference_phone', 'kyc_contacts');
    }
};
