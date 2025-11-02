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
        // Current structure:
        // - name (contains phone numbers)
        // - contact_type (contains type: primary, alternate, etc.)
        
        // Target structure:
        // - contact_number (should contain phone numbers)
        // - name (should contain person's name/type)
        
        // Use guards to avoid errors if the columns were already renamed or missing.
        // This makes the migration idempotent for environments that may be in different states.
        if (Schema::hasTable('user_reference_phone')) {
            // rename `name` -> `contact_number` only if `name` exists and `contact_number` does not
            if (Schema::hasColumn('user_reference_phone', 'name') && ! Schema::hasColumn('user_reference_phone', 'contact_number')) {
                Schema::table('user_reference_phone', function (Blueprint $table) {
                    $table->renameColumn('name', 'contact_number');
                });
            }

            // rename `contact_type` -> `name` only if `contact_type` exists and `name` does not
            if (Schema::hasColumn('user_reference_phone', 'contact_type') && ! Schema::hasColumn('user_reference_phone', 'name')) {
                Schema::table('user_reference_phone', function (Blueprint $table) {
                    $table->renameColumn('contact_type', 'name');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_reference_phone')) {
            // reverse: contact_number -> name, and name -> contact_type
            if (Schema::hasColumn('user_reference_phone', 'contact_number') && ! Schema::hasColumn('user_reference_phone', 'name')) {
                Schema::table('user_reference_phone', function (Blueprint $table) {
                    $table->renameColumn('contact_number', 'name');
                });
            }

            if (Schema::hasColumn('user_reference_phone', 'name') && ! Schema::hasColumn('user_reference_phone', 'contact_type')) {
                Schema::table('user_reference_phone', function (Blueprint $table) {
                    $table->renameColumn('name', 'contact_type');
                });
            }
        }
    }
};

