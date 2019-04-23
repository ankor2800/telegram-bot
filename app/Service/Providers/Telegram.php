<?php
namespace App\Service\Providers;


class Telegram
{
    /** @var array telegram config */
    protected $settings;

    /** @var array массив типов HTTP-заголовков */
    const HTTPHEADER = [
        'form-data' => 'Content-Type: multipart/form-data',
        'json' => 'Content-Type: application/json'
    ];

    public function __construct()
    {
        $this->settings = config('telegram');
    }

    /**
     * Метод запроса к Telegram Bot API
     * @param string $method API метод
     * @param string $header тип HTTP заголовка
     * @param array $params массив параметров запроса
     * @param bool|float $fileSize Ожидаемый размер файла в байтах при загрузке файла
     * @return bool|mixed
     */
    public function query($method, $header = 'json', $params = [], $fileSize = false)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->getUrl($method));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [self::HTTPHEADER[$header]]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if ($fileSize) {
            curl_setopt($curl, CURLOPT_INFILESIZE , $fileSize);
        }

        if ($this->settings['use_proxy']) {
            curl_setopt($curl, CURLOPT_PROXY, $this->settings['proxy']);
        }

        $result = json_decode(curl_exec($curl));


        return $result ?? false;
    }

    /**
     * Метод генерации ссылки на API бота
     * @param string $method API метод
     * @return string
     */
    private function getUrl($method)
    {
        return $this->settings['url'].$this->settings['token'].'/'.$method;
    }
}
