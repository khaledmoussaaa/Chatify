<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_id',
        'message',
        'photo',
        'file',
        'seen',
    ];

    // Relations
    public function messages()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lastMessage()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
