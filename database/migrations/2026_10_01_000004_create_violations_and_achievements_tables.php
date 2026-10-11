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
        Schema::create('violation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->integer('points')->default(10);
            $table->text('sanction')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('violation_category_id')->constrained('violation_categories')->cascadeOnDelete();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('violation_date');
            $table->integer('points')->default(10);
            $table->text('description')->nullable();
            $table->text('sanction_given')->nullable();
            $table->string('status')->default('recorded')->index(); // recorded, resolved, follow_up_needed
            $table->text('follow_up_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('achievement_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('achievement_category_id')->constrained('achievement_categories')->cascadeOnDelete();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('level')->default('Sekolah'); // Sekolah, Kecamatan, Kabupaten/Kota, Provinsi, Nasional, Internasional
            $table->string('rank')->nullable(); // Juara 1, Juara 2, etc.
            $table->integer('points')->default(10);
            $table->date('event_date');
            $table->string('organizer')->nullable();
            $table->text('description')->nullable();
            $table->string('certificate_path')->nullable();
            $table->string('verification_status')->default('verified')->index(); // pending, verified, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('achievement_categories');
        Schema::dropIfExists('violations');
        Schema::dropIfExists('violation_categories');
    }
};
