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
        Schema::create('todos', function (Blueprint $table) {
            $table->bigIncrements('id'); // BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT
            $table->bigInteger('user_id')->unsigned(); // BIGINT(20) UNSIGNED NOT NULL
            $table->bigInteger('project_id')->unsigned()->nullable(); // BIGINT(20) UNSIGNED NULL DEFAULT NULL
            $table->bigInteger('parent_task_id')->unsigned()->nullable(); // BIGINT(20) UNSIGNED NULL DEFAULT NULL
            $table->string('task'); // VARCHAR(255) NOT NULL
            $table->text('notes')->nullable(); // TEXT NULL DEFAULT NULL
            $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo'); // ENUM('todo','in_progress','done') NOT NULL DEFAULT 'todo'
            $table->enum('priority', ['low', 'medium', 'high', 'focus'])->default('medium'); // ENUM('low','medium','high','focus') NOT NULL DEFAULT 'medium'
            $table->enum('eisenhower_quadrant', ['do', 'schedule', 'delegate', 'delete'])->nullable(); // ENUM('do','schedule','delegate','delete') NULL DEFAULT NULL
            $table->dateTime('due_date')->nullable(); // DATETIME NULL DEFAULT NULL
            $table->timestamp('completed_at')->nullable(); // TIMESTAMP NULL DEFAULT NULL
            $table->unsignedInteger('pomodoro_custom_duration')->nullable(); // INT(10) UNSIGNED NULL DEFAULT NULL
            $table->unsignedTinyInteger('pomodoro_cycles_completed')->default(0); // TINYINT(3) UNSIGNED NOT NULL DEFAULT '0'
            $table->timestamps(); // created_at TIMESTAMP NULL DEFAULT NULL, updated_at TIMESTAMP NULL DEFAULT NULL

            // Foreign Key Constraints
            // CONSTRAINT `todos_parent_task_id_foreign` FOREIGN KEY (`parent_task_id`) REFERENCES `todos` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
            $table->foreign('parent_task_id')
                  ->references('id')
                  ->on('todos')
                  ->onUpdate('no action')
                  ->onDelete('cascade');

            // CONSTRAINT `todos_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onUpdate('no action')
                  ->onDelete('cascade');

            // CONSTRAINT `todos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
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
        Schema::dropIfExists('todos');
    }
};

