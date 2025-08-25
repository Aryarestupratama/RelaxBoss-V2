<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->integer('day_number'); // Materi untuk hari ke-1, 2, 3, dst.
            $table->string('title');
            $table->text('content'); // Isi materi (bisa berisi HTML)
            $table->timestamps();
            
            $table->unique(['program_id', 'day_number']); // Pastikan hanya ada 1 materi per hari per program
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_materials');
    }
};
