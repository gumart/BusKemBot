<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use GuzzleHttp\Client;
use App\Services\{BusService, ParserService};

class CommunicationController extends Controller
{
    
    public static function sendCommand(User $user, Client $client, $text)
    {
        $last_command = $user->last_command;

        if($text == '/back' || $text == '/start') {
            $user->last_command = '/start';
            
            $user->save();

            $last_command = '/start';
        }

        if($last_command == '/start') {

            $user->last_command = '/choose_bus_type';

            $user->save();

            $client->request('GET', 'sendMessage', ['query' => [
                'chat_id' => $user->chat_id,
                'text' => 'Выберите тип маршрутного транспортного стредства'
            ]]);
        
        } elseif($last_command == '/choose_bus_type') {

            $user->last_command = '/choose_route_number';

            $user->save();

            BusService::chooseBusType($user, $text);

            $client->request('GET', 'sendMessage', ['query' => [
                'chat_id' => $user->chat_id,
                'text' => 'Укажите номер маршрута'
            ]]);
        
        } elseif($last_command == '/choose_route_number') {

            $user->last_command = '/choose_stop';

            $user->save();

            BusService::chooseRouteNumber($user, $text);

            $stop_list = ParserService::getStops($user);

            $client->request('GET', 'sendMessage', ['query' => [
                'chat_id' => $user->chat_id,
                'text' => $stop_list
            ]]);

        } elseif($last_command == '/choose_stop') {
            
            $user->last_command = '/start';

            $user->save();

            $comings = ParserService::getComings($user, $text);

            $client->request('GET', 'sendMessage', ['query' => [
                'chat_id' => $user->chat_id,
                'text' => $comings
            ]]);
        }
    }
}
