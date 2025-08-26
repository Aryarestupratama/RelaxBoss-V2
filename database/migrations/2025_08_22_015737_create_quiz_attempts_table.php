<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Menyimpan skor dan interpretasi untuk setiap sub_scale
            // Contoh: {"Fisik": {"score": 15, "interpretation": "Tinggi"}, "Emosional": {...}}
            $table->json('results')->nullable();

            // Menyimpan cerita/konteks yang diberikan pengguna jika skornya tinggi
            $table->text('user_context')->nullable();

            // Menyimpan rekomendasi lengkap yang dihasilkan oleh AI
            $table->text('ai_recommendation')->nullable();

            // Menyimpan ringkasan singkat dari AI untuk "rekam medis"
            $table->text('ai_summary')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};