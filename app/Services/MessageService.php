<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\{User, MessageUpdate};
use App\Http\Controllers\CommunicationController;

class MessageService
{
    
    private $baseUrl;
    private $token;
    private $client;
    private $isPalindrome = False;
    private $chatId;
    private $phrase;

    public function __construct()
    {
        $this->baseUrl = env('TELEGRAM_API_URL');
        $this->token = env('TELEGRAM_BOT_TOKEN');

        $this->client = new Client(
            ['base_uri' => $this->baseUrl . 'bot' . $this->token . '/']
        );
    }

    private function checkUpdate()
    {
        $message_update = MessageUpdate::where('update_id', $this->update_id)->get();

        if(count($message_update));
    }

    private function createOrUpdateUser()
    {
        if(count(User::where('chat_id', $this->telegram_id)->get())==0) {
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'username' => $this->username,
                'chat_id' => $this->telegram_id,
                'last_command' => '/start'
            ]);

        } else {
            $user = User::where('chat_id', $this->telegram_id)->first();

            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->username = $this->username;

            $user->save();
        }

        return $user;
    }

    public function getMessage()
    {
        $message_update = MessageUpdate::all()->first();

        if($message_update != null) {
            $update_id = $message_update->update_id;

        } else {
            $update_id = -2;
        }

        $response = $this->client->request('GET', 'getUpdates', [
            'query' => [
                'offset' => $update_id + 1,
            ]
        ]);
        
        if($response->getStatusCode() === 200){
            
            $messages = json_decode($response->getBody()->getContents(), true);

            //dd($messages);

            foreach($messages['result'] as $message) {

                $this->update_id = $message['update_id'];

                if($message_update != null) {
                    $message_update->update_id = $this->update_id;
                    
                    $message_update->save();
                } else {
                    $message_update = MessageUpdate::create([
                        'update_id' => $this->update_id
                    ]);
                }

                if(array_key_exists('edited_message', $message)) {    
                    $this->chatId = $message['edited_message']['chat']['id'];
                    $this->text = $message['edited_message']['text'];
                    $this->first_name =$message['edited_message']['from']['first_name'];
                    $this->last_name = $message['edited_message']['from']['last_name'];
                    $this->telegram_id = $message['edited_message']['from']['id'];
                    
                    if(array_key_exists('username', $message['edited_message']['from'])) {
                        $this->username = $message['edited_message']['from']['username'];
                    } else {
                        $this->username = null;
                    }
                    
                } else {
                    $this->chatId = $message['message']['chat']['id'];
                    $this->text = $message['message']['text'];
                    $this->first_name = $message['message']['from']['first_name'];
                    $this->last_name = $message['message']['from']['last_name'];
                    $this->telegram_id = $message['message']['from']['id'];
                    
                    if(array_key_exists('username', $message['message']['from'])) {
                        $this->username = $message['message']['from']['username'];
                    } else {
                        $this->username = null;
                    }
                }

                $user = $this->createOrUpdateUser();

                CommunicationController::sendCommand($user, $this->client, $this->text);
            }
        }
    }
}
