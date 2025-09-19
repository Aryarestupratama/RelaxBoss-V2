<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_actions', function (Blueprint $table) {
            $table->id();
            // Setiap aksi dipicu oleh sebuah pesan konsultasi
            $table->foreignId('ai_consultation_message_id')->constrained()->onDelete('cascade');
            $table->string('function_name'); // Nama fungsi yang dipanggil, misal: 'updateTaskDetails'
            $table->json('arguments'); // Argumen yang dikirim dalam format JSON
            $table->enum('status', ['success', 'failure']);
            $table->text('response')->nullable(); // Respons dari eksekusi fungsi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_actions');
    }
};
