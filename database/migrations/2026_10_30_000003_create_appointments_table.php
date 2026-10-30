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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('request_code')->unique(); // e.g. CLM-2041
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('visit_date');
            $table->string('time_slot')->default('Morning'); // 'Morning', 'Afternoon'
            $table->string('cover_type')->default('Cashless treatment'); // 'Discounted consultation', 'Cashless treatment'
            $table->string('status')->default('awaiting_review'); // 'awaiting_review', 'sent_to_clinic', 'declined'
            $table->timestamp('received_at')->useCurrent();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('decline_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

