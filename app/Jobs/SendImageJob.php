<?php

namespace App\Jobs;

use App\Service\Image;
use App\Service\Telegram;

class SendImageJob extends Job
{
    /** @var Image  */
    protected $image;
    /** @var Telegram */
    protected $telegram;

    /**
     * SendMessageJob constructor.
     * @param string $text text for image
     */
    public function __construct($text)
    {
        $this->image = new Image($text);
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
        $image = $this->image->createImage();
        $this->telegram->sendPhoto($image);
    }
}
