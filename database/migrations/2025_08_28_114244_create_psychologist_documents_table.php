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
        Schema::create('psychologist_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('ID Psikolog')->constrained()->onDelete('cascade');
            $table->string('title'); // Contoh: "Sertifikat Terapi CBT"
            $table->string('file_path'); // Path ke file yang disimpan
            $table->boolean('is_public')->default(false); // Kontrol visibilitas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychologist_documents');
    }
};
