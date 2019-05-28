<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendImageJob;
use App\Jobs\SendMessageJob;

class BotController extends Controller
{
    /**
     * Проверка токена авторизации бота
     * GET /bot/check
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $telegram = new \App\Service\Telegram();
        $res      = $telegram->getMe();

        return response()->json($res, 200);
    }

    /**
     * Получение обновлений чата
     * GET /bot/updates
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpdates()
    {
        $telegram = new \App\Service\Telegram();
        $res      = $telegram->getUpdates();

        return response()->json($res, 200);
    }

    /**
     * Создание изображения с наложением текста
     * отправка его сообщением в telegram
     * PUT /bot/send/image?text=
     * @param Request $request входящие параметры
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendImage(Request $request)
    {
        dispatch(
            new SendImageJob($request->get('text'))
        );

        return response()->json(true, 200);
    }

    /**
     * Создание изображения с наложением текста
     * отправка его сообщением в telegram
     * PUT /bot/send/message?text=
     * @param Request $request входящие параметры
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $this->validate($request, ['text' => 'required',]);

        dispatch(
            new SendMessageJob($request->get('text'))
        );

        return response()->json(true, 200);
    }
}
