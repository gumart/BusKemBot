<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\{User, MessageUpdate};

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

    private function createOrUpdateUser()
    {
        if(count(User::where('chat_id', $this->telegram_id)->get())==0) {
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'username' => $this->username,
                'chat_id' => $this->telegram_id
            ]);

            $message_update = MessageUpdate::create([
                'update_id' => $this->update_id,
                'user_id' => $user->id
            ]);

            $user->message_update_id = $message_update->id;

            $user->save();

        } else {
            $user = User::where('chat_id', $this->telegram_id)->first();

            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->username = $this->username;

            $user->save();

            $message_update = $user->message_update()->first();

            $message_update->update_id = $this->update_id;

            $message_update->save();
        }
    }

    public function getMessage()
    {
        $response = $this->client->request('GET', 'getUpdates', [
            'query' => [
                'offset' => -1,
            ]
        ]);
        
        if($response->getStatusCode() === 200){
            $messages = json_decode($response->getBody()->getContents(), true);

            $this->update_id = $messages['result'][0]['update_id'];
            
            if(array_key_exists('edited_message', $messages['result'][0])) {    
                $this->chatId = $messages['result'][0]['edited_message']['chat']['id'];
                $this->phrase = $messages['result'][0]['edited_message']['text'];
                $this->first_name = $messages['result'][0]['edited_message']['from']['first_name'];
                $this->last_name = $messages['result'][0]['edited_message']['from']['last_name'];
                $this->telegram_id = $messages['result'][0]['edited_message']['from']['id'];
                
                if(array_key_exists('username', $messages['result'][0]['edited_message']['from'])) {
                    $this->username = $messages['result'][0]['edited_message']['from']['username'];
                } else {
                    $this->username = null;
                }
                
            } else {
                $this->chatId = $messages['result'][0]['message']['chat']['id'];
                $this->phrase = $messages['result'][0]['message']['text'];
                $this->first_name = $messages['result'][0]['message']['from']['first_name'];
                $this->last_name = $messages['result'][0]['message']['from']['last_name'];
                $this->telegram_id = $messages['result'][0]['message']['from']['id'];
                
                if(array_key_exists('username', $messages['result'][0]['message']['from'])) {
                    $this->username = $messages['result'][0]['message']['from']['username'];
                } else {
                    $this->username = null;
                }
            }
            
            $this->createOrUpdateUser();
        }
    }
}
