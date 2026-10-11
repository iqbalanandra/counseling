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
        Schema::create('counseling_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Pribadi, Sosial, Belajar, Karier
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('counseling_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('counseling_type_id')->constrained('counseling_types')->cascadeOnDelete();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('preferred_date');
            $table->string('preferred_time')->nullable(); // e.g. '09:00 - 10:00'
            $table->dateTime('scheduled_datetime')->nullable();
            $table->text('complaint_summary'); // Private student note/complaint
            $table->string('status')->default('pending')->index(); // pending, approved, rescheduled, rejected, completed, cancelled
            $table->text('notes')->nullable(); // BK / counselor notes
            $table->text('rescheduled_reason')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('counseling_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counseling_request_id')->nullable()->constrained('counseling_requests')->nullOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('counseling_type_id')->constrained('counseling_types')->cascadeOnDelete();
            $table->dateTime('session_date');
            $table->string('venue')->default('Ruang BK');
            $table->string('status')->default('scheduled')->index(); // scheduled, ongoing, completed, cancelled
            $table->timestamps();
        });

        Schema::create('counseling_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counseling_session_id')->constrained('counseling_sessions')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
            $table->string('topic');
            $table->longText('problem_description'); // CONFIDENTIAL
            $table->longText('counseling_notes'); // CONFIDENTIAL
            $table->longText('assessment_diagnosis')->nullable(); // CONFIDENTIAL
            $table->longText('recommendations')->nullable();
            $table->longText('follow_up_plan')->nullable();
            $table->boolean('is_confidential')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counseling_records');
        Schema::dropIfExists('counseling_sessions');
        Schema::dropIfExists('counseling_requests');
        Schema::dropIfExists('counseling_types');
    }
};
