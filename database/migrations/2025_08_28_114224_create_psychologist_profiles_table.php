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
        Schema::create('psychologist_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable()->comment('Gelar, misal: M.Psi., Psikolog');
            $table->string('str_number')->nullable();
            $table->string('sipp_number')->nullable();
            $table->text('bio')->nullable();
            $table->string('domicile')->nullable();
            $table->string('education')->nullable();
            $table->string('practice_location')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->text('intro_template_message')->nullable();
            $table->boolean('is_available')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychologist_profiles');
    }
};
