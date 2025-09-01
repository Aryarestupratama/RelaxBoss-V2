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
        Schema::create('psychologist_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('ID Psikolog')->constrained()->onDelete('cascade');
            $table->enum('type', ['online', 'offline']);
            $table->decimal('price_per_session', 10, 2)->default(0);
            $table->integer('duration_per_session_minutes')->default(50);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychologist_services');
    }
};
