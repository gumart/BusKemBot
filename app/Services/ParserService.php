<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\{User, VehicleType, RouteNumber};

class ParserService
{

    public static function getDate($list_of_dates)
    {
        date_default_timezone_set('Asia/Novosibirsk');
       
        $current_date = date('H:i');

        foreach($list_of_dates as $date) {
            if($date > $current_date) {
                return "\nБлижайший автобус пребудет в " . $date;
            }
        }

        return "\nБлижайший автобус пребудет завтра в: " . $list_of_dates[0];
    }

    public static function getStops(User $user)
    {
        $message="";

        $number_of_stops=0;

        $client = new Client([
            'base_uri' => 'https://bus.vse42.ru/api/kema/routedirections/'
        ]);

        $response = $client->request('GET', '',[
            'query' => [
                'route' => $user->route_number->route_number,
                'vehicleType' => $user->vehicle_type->vehicle_type
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if(array_key_exists('data', $data)) {

            foreach($data['data'] as $route) {
                $message .= "Список остановок для маршрута " . $route['directionStart'] . " - " . $route['directionStop'] . ":\n\n";
                
                foreach($route['stops'] as $stop) {
                    $message .= $stop['name'] . " - " . (string)++$number_of_stops . "\n";
                }

                $message .= "\n\n";
                $message .= "Выберите номер остановки";
            }
        } else {
            $message = "Данный маршрут не существует для такого типа транспортного средства. Попробуйте указать верный маршрут";
            $user->last_command = '/choose_route_number';

            $user->save();
        }
        return $message;
    }

    public static function getComings(User $user, $number_of_stop)
    {
        $message="";

        $list_of_dates = [];

        $numbers_of_stops=0;

        $flag = False;

        $client = new Client([
            'base_uri' => 'https://bus.vse42.ru/api/kema/routedirections/'
        ]);

        $response = $client->request('GET', '',[
            'query' => [
                'route' => $user->route_number->route_number,
                'vehicleType' => $user->vehicle_type->vehicle_type
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        foreach($data['data'] as $route) {
            foreach($route['stops'] as $stop) {
                if(++$numbers_of_stops == $number_of_stop) {
                    $message .= "Расписание остановки для выбранного вами автобуса: \n\n";
                    foreach($stop['comings'] as $coming) {
                        $message .= substr($coming, 11, -3)  . "\n";
                        $list_of_dates[] = substr($coming, 11, -3);
                    }
                    $flag = True;

                    $message .= ParserService::getDate($list_of_dates);

                    break;
                }
            }

            if($flag == True) {
                break;
            }
            $message .= "\n\n";
        }

        if($flag == False) {
            $message = "Вы ввели некорректные данные. Попробуйте ещё";
            $user->last_command = '/choose_stop';
            $user->save();
        }

        return $message;
    }
}