<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('current_day')->default(1); // Melacak progres hari pengguna
            $table->timestamp('completed_at')->nullable(); // Diisi jika program sudah selesai
            $table->timestamps();

            $table->unique(['program_id', 'user_id']); // Pastikan user hanya bisa mendaftar sekali
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_enrollments');
    }
};
