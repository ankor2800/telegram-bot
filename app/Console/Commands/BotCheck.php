<?php
namespace App\Console\Commands;

use App\Service\Telegram;
use Illuminate\Console\Command;

class BotCheck extends Command
{
    protected $signature   = 'bot:check';
    protected $description = 'Команда для проверки доступности телеграм бота';
    private $telegram;

    public function __construct(Telegram $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function fire()
    {
        $res = $this->telegram->getMe();

        if (!$res->ok) {
            echo 'Error connect bot';
        } else {
            echo json_encode($res->result)."\n";
        }
    }
}
