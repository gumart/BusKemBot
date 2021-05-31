<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteNumber extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'routes_numbers';

    protected $fillable = [
        'route_number',
        'user_id'
    ]

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
