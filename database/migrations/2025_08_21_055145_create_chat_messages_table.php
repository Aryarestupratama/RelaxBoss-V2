<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('conversation_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('sender_type', ['user', 'ai']);
            $table->enum('message_type', ['text', 'audio'])->default('text');
            $table->text('message_text')->nullable();
            $table->string('audio_url', 2048)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('conversation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
}
