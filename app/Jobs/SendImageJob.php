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
    /** @var null|string */
    protected $text;

    /**
     * SendMessageJob constructor.
     * @param null|string $text текст накладываемый на изображение
     */
    public function __construct($text)
    {
        $this->image = new Image();
        $this->telegram = new Telegram();
        $this->text = $text;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        if ($this->text) {
            $this->image->setText($this->text);
        }

        $image = $this->image->createImage()->getImage();
        $this->telegram->sendPhoto($image);
    }
}
