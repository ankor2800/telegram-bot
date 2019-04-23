<?php

namespace App\Jobs;

use App\Service\Telegram;

class SendMessageJob extends Job
{
    /** @var Telegram */
    protected $telegram;
    /** @var string  */
    protected $text;

    /**
     * SendMessageJob constructor.
     * @param string $text text for image
     */
    public function __construct($text)
    {
        $this->text = $text;
        $this->telegram = new Telegram();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        $this->telegram->sendMessage($this->text);
    }
}
