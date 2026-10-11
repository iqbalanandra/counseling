<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_homeroom_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->unique(['class_id', 'teacher_id']);
            $table->timestamps();
        });

        // Migrate existing homeroom_teacher_id from classes to pivot
        if (Schema::hasTable('classes')) {
            $existing = DB::table('classes')->whereNotNull('homeroom_teacher_id')->get();
            foreach ($existing as $cls) {
                DB::table('class_homeroom_teachers')->insertOrIgnore([
                    'class_id' => $cls->id,
                    'teacher_id' => $cls->homeroom_teacher_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_homeroom_teachers');
    }
};
