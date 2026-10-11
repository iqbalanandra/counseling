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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. 2026/2027
            $table->string('semester')->default('ganjil'); // ganjil, genap
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nip')->nullable()->unique();
            $table->string('name');
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->string('phone')->nullable();
            $table->string('position')->nullable(); // Guru BK, Wali Kelas, Kepala Sekolah, etc.
            $table->timestamps();
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. 7A, 8B, 9C
            $table->string('grade'); // 7, 8, 9
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nis')->unique();
            $table->string('nisn')->nullable()->unique();
            $table->string('name');
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('status')->default('active')->index(); // active, graduated, transferred
            $table->integer('total_violation_points')->default(0);
            $table->integer('total_achievement_points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('academic_years');
    }
};
