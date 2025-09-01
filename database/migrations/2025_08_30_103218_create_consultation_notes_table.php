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
        Schema::create('consultation_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_session_id')->constrained('consultation_sessions')->onDelete('cascade');
            $table->text('notes'); // Kolom untuk menyimpan catatan dari psikolog
            $table->timestamps();
            
            // Setiap sesi hanya boleh punya satu catatan
            $table->unique('consultation_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_notes');
    }
};