<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's drop all existing foreign keys on loan_id
        Schema::table('user_reference_phone', function (Blueprint $table) {
            // Try to drop if exists (will not fail if doesn't exist)
            try {
                DB::statement('ALTER TABLE user_reference_phone DROP FOREIGN KEY kyc_contacts_app_id_foreign');
            } catch (\Exception $e) {
                // Foreign key doesn't exist, that's okay
            }
        });
        
        Schema::table('user_reference_phone', function (Blueprint $table) {
            // Change loan_id from bigInteger to string
            DB::statement('ALTER TABLE user_reference_phone MODIFY loan_id VARCHAR(20)');
            
            // Add foreign key to loan_id column referencing kyc_applications.loan_id
            $table->foreign('loan_id')
                  ->references('loan_id')
                  ->on('kyc_applications')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_reference_phone', function (Blueprint $table) {
            // Drop the string foreign key
            $table->dropForeign(['loan_id']);
            
            // Change back to bigInteger
            DB::statement('ALTER TABLE user_reference_phone MODIFY loan_id BIGINT UNSIGNED');
            
            // Restore original foreign key
            $table->foreign('loan_id')->references('id')->on('kyc_applications')->onDelete('cascade');
        });
    }
};
