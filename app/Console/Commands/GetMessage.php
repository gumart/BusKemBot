<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MessageController;

class GetMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $message_controller = new MessageController;

        $result = $message_controller->getMessage();

        return 0;
    }
}
