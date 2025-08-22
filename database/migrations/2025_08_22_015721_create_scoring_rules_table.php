<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scoring_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->string('sub_scale');
            $table->integer('min_score');
            $table->integer('max_score');
            $table->string('interpretation'); // Contoh: "Stres Tingkat Sedang"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scoring_rules');
    }
};
