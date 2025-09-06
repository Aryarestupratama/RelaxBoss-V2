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
        Schema::create('task_files', function (Blueprint $table) {
            $table->bigIncrements('id'); // BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT
            $table->bigInteger('todo_id')->unsigned(); // BIGINT(20) UNSIGNED NOT NULL
            $table->string('file_category')->default('lainnya'); // VARCHAR(255) NOT NULL DEFAULT 'lainnya'
            $table->string('file_type'); // VARCHAR(255) NOT NULL
            $table->string('file_path'); // VARCHAR(255) NOT NULL
            $table->string('file_name')->nullable(); // VARCHAR(255) NULL DEFAULT NULL
            $table->timestamps(); // created_at TIMESTAMP NULL DEFAULT NULL, updated_at TIMESTAMP NULL DEFAULT NULL

            // Foreign Key Constraint
            // CONSTRAINT `task_files_todo_id_foreign` FOREIGN KEY (`todo_id`) REFERENCES `todos` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
            $table->foreign('todo_id')
                  ->references('id')
                  ->on('todos')
                  ->onUpdate('no action') // Corresponds to ON UPDATE NO ACTION
                  ->onDelete('cascade'); // Corresponds to ON DELETE CASCADE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_files');
    }
};

