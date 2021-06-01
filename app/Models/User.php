<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'chat_id',
        'last_command',
        'vehicle_type_id',
        'route_number_id',
        'stop_id'
    ];

    public function vehicle_type()
    {
        return $this->hasOne(VehicleType::class, 'id', 'vehicle_type_id');
    }

    public function route_number()
    {
        return $this->hasOne(RouteNumber::class, 'id', 'route_number_id');
    }

    public function stop()
    {
        return $this->hasOne(Stop::class, 'id', 'stop_id');
    }
}
