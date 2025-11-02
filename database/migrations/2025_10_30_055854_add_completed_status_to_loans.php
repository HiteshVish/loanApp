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
        // The status column is already a string type, so it can accept 'completed' value
        // No need to modify schema, just add comment for documentation
        Schema::table('loan_details', function (Blueprint $table) {
            // Status column already exists and can store 'completed' status
            // Possible values: pending, approved, rejected, completed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_details', function (Blueprint $table) {
            // No rollback needed as we're not changing the schema
        });
    }
};
