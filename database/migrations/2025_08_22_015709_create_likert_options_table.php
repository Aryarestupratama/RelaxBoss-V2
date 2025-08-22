<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likert_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->string('label'); // Contoh: "Sangat Setuju"
            $table->integer('value'); // Contoh: 4
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likert_options');
    }
};