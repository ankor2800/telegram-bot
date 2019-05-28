<?php
namespace App\Service;

class Telegram
{
    /** @var Providers\Telegram */
    protected $provider;
    /** @var array telegram config */
    protected $settings;

    public function __construct()
    {
        $this->settings = config('telegram');
        $this->provider = new Providers\Telegram;
    }

    /**
     * API метод для проверки токена авторизации бота
     * @return bool|mixed
     */
    public function getMe()
    {
        return $this->provider->query('getMe');
    }

    /**
     * API метод для получения обновлений
     * Под обновлением подразумевается действие,
     * совершённое с ботом — например, получение сообщения от пользователя.
     * @return bool|mixed
     */
    public function getUpdates()
    {
        return $this->provider->query('getUpdates');
    }

    /**
     * API метод отправки ботом текстового сообщения
     * @param string $message текст сообщения
     * @return mixed
     * @throws \Exception
     */
    public function sendMessage($message)
    {
        $response = $this->provider->query(
            'sendMessage',
            'json',
            json_encode(['chat_id' => $this->settings['chat_id'], 'text' => $message])
        );

        if (!$response) {
            throw new \Exception('Error send message');
        }

        if ($response->ok === false) {
            throw new \Exception($response->description);
        }

        return $response->ok;
    }

    /**
     * API метод отправки ботом изображения
     * @param \Illuminate\Support\Collection $image коллекция необходимых данных об изображении
     * @return mixed
     * @throws \Exception
     */
    public function sendPhoto($image)
    {
        $response = $this->provider->query(
            'sendPhoto',
            'form-data',
            [
                'chat_id' => $this->settings['chat_id'],
                'photo'   => new \CURLFile($image->get('base'), $image->get('mime'), $image->get('name')),
            ],
            $image->get('size')
        );

        if (!$response) {
            throw new \Exception('Error send file');
        }

        if ($response->ok === false) {
            throw new \Exception($response->description);
        }

        if ($this->settings['remove_image']) {
            Image::remove($image->get('base'));
        }

        return $response->ok;
    }
}
