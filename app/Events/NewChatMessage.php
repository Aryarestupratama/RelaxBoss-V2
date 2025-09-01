    <?php

    namespace App\Events;
    
    use App\Models\ChatConsultation;
    use Illuminate\Broadcasting\Channel;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Broadcasting\PresenceChannel;
    use Illuminate\Broadcasting\PrivateChannel;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Queue\SerializesModels;
    
    class NewChatMessage implements ShouldBroadcast
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;
    
        public $message;
    
        /**
         * Create a new event instance.
         */
        public function __construct(ChatConsultation $message)
        {
            $this->message = $message;
        }
    
        /**
         * Get the channels the event should broadcast on.
         *
         * @return array<int, \Illuminate\Broadcasting\Channel>
         */
        public function broadcastOn(): array
        {
            // Kita menggunakan PrivateChannel agar hanya user yang berhak (pasien & psikolog)
            // yang bisa mendengarkan channel ini.
            return [
                new PrivateChannel('consultation.' . $this->message->consultation_session_id),
            ];
        }
    
        /**
         * Menentukan nama event yang akan disiarkan.
         */
        public function broadcastAs(): string
        {
            return 'new.message';
        }
    }
    
