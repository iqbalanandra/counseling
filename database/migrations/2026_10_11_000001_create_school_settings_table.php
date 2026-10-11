<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('SMP Negeri 1 Metro');
            $table->string('portal_name')->default('Konseling-Yu');
            $table->string('tagline')->nullable()->default('Portal Bimbingan & Konseling Terpadu');
            $table->string('npsn')->nullable()->default('20104567');
            $table->string('logo_url')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable()->default('(021) 7654321');
            $table->string('whatsapp')->nullable()->default('0812-3456-7890');
            $table->string('email')->nullable()->default('info@smpn1metro.sch.id');
            $table->string('website')->nullable()->default('https://smpn1metro.sch.id');
            $table->string('principal_name')->nullable()->default('Drs. Bambang Sutrisno, M.Pd');
            $table->text('welcome_message')->nullable();
            $table->timestamps();
        });

        // Seed default initial row
        DB::table('school_settings')->insert([
            'school_name' => 'SMP Negeri 1 Metro',
            'portal_name' => 'Konseling-Yu',
            'tagline' => 'Portal Bimbingan & Konseling Terpadu',
            'npsn' => '20104567',
            'logo_url' => null,
            'address' => 'Jl. Merdeka No. 45, Metro, Lampung',
            'phone' => '(021) 7654321',
            'whatsapp' => '0812-3456-7890',
            'email' => 'info@smpn1metro.sch.id',
            'website' => 'https://smpn1metro.sch.id',
            'principal_name' => 'Drs. Bambang Sutrisno, M.Pd',
            'welcome_message' => 'Ruang aman dan nyaman untuk konsultasi, bimbingan belajar, eksplorasi masa depan, dan pendampingan karakter.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
