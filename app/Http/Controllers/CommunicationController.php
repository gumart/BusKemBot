<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use GuzzleHttp\Client;
use App\Services\{BusService, ParserService};

class CommunicationController extends Controller
{

    public static function convertBusType($text)
    {
        if($text=='Автобус') {
            return 'b';
        } elseif($text=='Маршрутка') {
            return 'rt';
        } elseif($text=='Троллейбус') {
            return 'tb';
        } elseif($text=='Трамвай') {
            return 'tr';
        } else {
            return '';
        }
    }

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

            $reply_markup = [
                'keyboard' => [
                    ['Автобус', 'Маршрутка'],
                    ['Троллейбус', 'Трамвай'],
                ]
            ];

            $encoded_markup = json_encode($reply_markup);

            $client->request('GET', 'sendMessage', ['query' => [
                'chat_id' => $user->chat_id,
                'text' => 'Выберите тип маршрутного транспортного стредства',
                'reply_markup' => $encoded_markup
            ]]);
        
        } elseif($last_command == '/choose_bus_type') {

            $user->last_command = '/choose_route_number';

            $user->save();

            $reply_markup = [
                'keyboard' => [
                    ['Назад']
                ]
            ];

            $reply_markup_bus_type = [
                'keyboard' => [
                    ['Автобус', 'Маршрутка'],
                    ['Троллейбус', 'Трамвай'],
                ]
            ];

            $encoded_markup = json_encode($reply_markup);

            $text = CommunicationController::convertBusType($text);

            if($text!='') {
                BusService::chooseBusType($user, $text);

                $client->request('GET', 'sendMessage', ['query' => [
                    'chat_id' => $user->chat_id,
                    'text' => 'Укажите номер маршрута',
                    'reply_markup' => $encoded_markup
                ]]);
            } else {
                $encoded_markup = json_encode($reply_markup_bus_type);
                $client->request('GET', 'sendMessage', ['query' => [
                    'chat_id' => $user->chat_id,
                    'text' => 'Вы указали неверный тип транспортного средства. Пожалуйста, воспользуйтесь клавиатурой, или введите правильное название автобуса',
                    'reply_markup' => $encoded_markup
                ]]);
            }

        } elseif($last_command == '/choose_route_number') {
            
            $user->last_command = '/choose_stop';

            $user->save();

            $reply_markup = [
                'keyboard' => [
                    ['Назад']
                ]
            ];

            $reply_markup_bus_type = [
                'keyboard' => [
                    ['Автобус', 'Маршрутка'],
                    ['Троллейбус', 'Трамвай'],
                ]
            ];

            if($text != 'Назад') {

                $encoded_markup = json_encode($reply_markup);

                BusService::chooseRouteNumber($user, $text);

                $stop_list = ParserService::getStops($user);

                $client->request('GET', 'sendMessage', ['query' => [
                    'chat_id' => $user->chat_id,
                    'text' => $stop_list,
                    'reply_markup' => $encoded_markup
                ]]);
            } else {

                $encoded_markup = json_encode($reply_markup);
                
                $user->last_command = '/choose_route_number';
                
                $user->save();

                $client->request('GET', 'sendMessage', ['query' => [
                    'chat_id' => $user->chat_id,
                    'text' => 'Выберите тип маршрутного транспортного стредства',
                    'reply_markup' => $encoded_markup
                ]]);
            }

        } elseif($last_command == '/choose_stop') {
            
            $user->last_command = '/start';

            $user->save();

            $reply_markup = [
                'remove_keyboard' => true
            ];

            $encoded_markup = json_encode($reply_markup);

            $comings = ParserService::getComings($user, $text);

            $client->request('GET', 'sendMessage', ['query' => [
                'chat_id' => $user->chat_id,
                'text' => $comings,
                'reply_markup' => $encoded_markup
            ]]);
        }
    }
}
