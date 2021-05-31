<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MessageService;

class MessageController extends Controller
{
    public function getMessage()
    {

        $message_service = new MessageService;

        $result = $message_service->getMessage();

        return response(200);
    }
}
