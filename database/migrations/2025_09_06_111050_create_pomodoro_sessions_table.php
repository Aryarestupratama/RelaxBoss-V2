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
        Schema::create('pomodoro_sessions', function (Blueprint $table) {
            $table->bigIncrements('id'); // BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT
            $table->bigInteger('user_id')->unsigned(); // BIGINT(20) UNSIGNED NOT NULL
            $table->bigInteger('todo_id')->unsigned()->nullable(); // BIGINT(20) UNSIGNED NULL DEFAULT NULL
            $table->enum('type', ['work', 'short_break', 'long_break'])->default('work'); // ENUM('work','short_break','long_break') NOT NULL DEFAULT 'work'
            $table->timestamp('start_time'); // TIMESTAMP NOT NULL
            $table->timestamp('end_time')->nullable(); // TIMESTAMP NULL DEFAULT NULL
            $table->unsignedInteger('duration_minutes')->default(25); // INT(10) UNSIGNED NOT NULL DEFAULT '25'
            $table->enum('status', ['scheduled', 'running', 'completed', 'interrupted'])->default('scheduled'); // ENUM('scheduled','running','completed','interrupted') NOT NULL DEFAULT 'scheduled'
            $table->unsignedInteger('remaining_seconds')->nullable(); // INT(10) UNSIGNED NULL DEFAULT NULL
            $table->timestamps(); // created_at TIMESTAMP NULL DEFAULT NULL, updated_at TIMESTAMP NULL DEFAULT NULL

            // Foreign Key Constraints
            // CONSTRAINT `pomodoro_sessions_todo_id_foreign` FOREIGN KEY (`todo_id`) REFERENCES `todos` (`id`) ON UPDATE NO ACTION ON DELETE SET NULL
            $table->foreign('todo_id')
                  ->references('id')
                  ->on('todos')
                  ->onUpdate('no action')
                  ->onDelete('set null'); // Corresponds to ON DELETE SET NULL

            // CONSTRAINT `pomodoro_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('no action')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pomodoro_sessions');
    }
};