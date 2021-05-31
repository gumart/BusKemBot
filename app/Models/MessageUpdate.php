<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageUpdate extends Model
{

    public $timestamps = false;

    protected $table = 'messages_updates';

    use HasFactory;

    protected $fillable = [
        'update_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
