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
        Schema::create('kyc_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('kyc_applications')->onDelete('cascade');
            $table->string('contact_number');
            $table->string('contact_type')->nullable(); // primary, alternate, emergency, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_contacts');
    }
};
