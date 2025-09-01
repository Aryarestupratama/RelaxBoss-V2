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
        Schema::create('session_medical_records', function (Blueprint $table) {
            $table->foreignId('consultation_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_attempt_id')->constrained()->onDelete('cascade');
            $table->primary(['consultation_session_id', 'quiz_attempt_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_medical_records');
    }
};
