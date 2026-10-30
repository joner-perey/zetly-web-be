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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('clinic_name');
            $table->string('specialty');
            $table->decimal('consultation_fee', 10, 2)->default(0.00);
            $table->text('address')->nullable();
            $table->string('smc')->unique();
            $table->string('smc_status')->default('verified'); // 'verified', 'pending', 'flagged'
            $table->timestamp('smc_last_verified_at')->nullable();
            $table->string('status')->default('active'); // 'active', 'inactive'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};

