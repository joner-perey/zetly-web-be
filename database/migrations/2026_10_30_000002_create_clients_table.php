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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('client_code')->unique(); // e.g. ZT-10501
            $table->string('full_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable(); // 'Female', 'Male', 'Other'
            $table->string('contact_number')->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_id')->nullable(); // Policy / Certificate #
            $table->string('plan_type')->nullable(); // Corporate Comprehensive, etc.
            $table->boolean('cashless_eligible')->default(true);
            $table->foreignId('referrer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('onboarding_token', 100)->unique()->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('onboarding_status')->default('pending_activation'); // 'pending_activation', 'activated', 'expired'
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

