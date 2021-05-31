<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use GuzzleHttp\Client;

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

            $user->last_command = '/choose_route_number'

            $user->save();

            $client->request('GET', 'sendMessage', ['query' => [
                'chat_id' => $user->chat_id,
                'text' => 'Выбирает тип тс'
            ]]);
        }
    }
}
