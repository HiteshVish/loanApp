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
        // Drop existing foreign key on user_id
        Schema::table('user_locations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        // Rename user_id to loan_id and change type to VARCHAR
        Schema::table('user_locations', function (Blueprint $table) {
            $table->renameColumn('user_id', 'loan_id');
        });
        
        // Change loan_id to string and add foreign key
        DB::statement('ALTER TABLE user_locations MODIFY loan_id VARCHAR(20)');
        
        Schema::table('user_locations', function (Blueprint $table) {
            $table->foreign('loan_id')
                  ->references('loan_id')
                  ->on('kyc_applications')
                  ->onDelete('cascade');
        });
        
        // Make loan_id + created_at combination (remove unique constraint on loan_id alone)
        // This allows multiple locations per loan_id
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key
        Schema::table('user_locations', function (Blueprint $table) {
            $table->dropForeign(['loan_id']);
        });
        
        // Rename back and change type
        DB::statement('ALTER TABLE user_locations MODIFY loan_id BIGINT UNSIGNED');
        
        Schema::table('user_locations', function (Blueprint $table) {
            $table->renameColumn('loan_id', 'user_id');
        });
        
        // Add back original foreign key
        Schema::table('user_locations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};

