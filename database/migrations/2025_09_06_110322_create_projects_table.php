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
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id'); // BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT
            $table->bigInteger('user_id')->unsigned(); // BIGINT(20) UNSIGNED NOT NULL
            $table->string('name'); // VARCHAR(255) NOT NULL
            $table->text('description')->nullable(); // TEXT NULL DEFAULT NULL
            $table->timestamps(); // created_at TIMESTAMP NULL DEFAULT NULL, updated_at TIMESTAMP NULL DEFAULT NULL

            // Foreign Key Constraint
            // CONSTRAINT `projects_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('no action') // Corresponds to ON UPDATE NO ACTION
                  ->onDelete('cascade'); // Corresponds to ON DELETE CASCADE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

