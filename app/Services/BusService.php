<?php

namespace App\Services;

use App\Models\{User, VehicleType, RouteNumber};


class BusService
{

    public static function chooseBusType(User $user, $text)
    {
        $vehicle_type = $user->vehicle_type;

        if($vehicle_type==null) {
            $vehicle_type = VehicleType::create([
                'vehicle_type' => $text,
                'user_id' => $user->id
            ]);

            $user->vehicle_type_id = $vehicle_type->id;

            $user->save();
        } else {
            $vehicle_type->vehicle_type = $text;

            $vehicle_type->save();
        }
    }

    public static function chooseRouteNumber(User $user, $text)
    {
        $route_number = $user->route_number;

        if($route_number==null) {
            $route_number = RouteNumber::create([
                'route_number' => $text,
                'user_id' => $user->id
            ]);

            $user->route_number_id = $route_number->id;

            $user->save();
        } else {
            $route_number->route_number = $text;

            $route_number->save();
        }
    
    }
}