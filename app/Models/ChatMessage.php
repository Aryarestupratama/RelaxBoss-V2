<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    /**
     * Menonaktifkan kolom 'updated_at' karena tidak ada di migrasi Anda.
     */
    public const UPDATED_AT = null;

    /**
     * Properti $fillable menentukan kolom mana yang boleh diisi secara massal.
     * Ini adalah fitur keamanan untuk mencegah pengisian kolom yang tidak diinginkan.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'conversation_id',
        'user_id',
        'sender_type',
        'message_type',
        'message_text',
        'audio_url',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array', // Otomatis mengubah JSON menjadi array dan sebaliknya
    ];

    /**
     * Mendefinisikan relasi bahwa setiap pesan dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
