<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConsultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_session_id',
        'sender_id',
        'message',
    ];

    /**
     * Pesan ini milik satu sesi konsultasi.
     */
    public function session()
    {
        return $this->belongsTo(ConsultationSession::class, 'consultation_session_id');
    }

    /**
     * Pesan ini dikirim oleh satu User.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}