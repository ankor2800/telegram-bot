<?php

return [
    'url'          => 'https://api.telegram.org/bot',
    'token'        => env('T_BOT_TOKEN', ''),
    'use_proxy'    => env('USE_SOCKS5_PROXY', false),
    'proxy'        => env('SOCKS5_PROXY', ''),
    'chat_id'      => env('T_CHAT_ID', null),
    'remove_image' => env('IMG_REMOVE', false),
];
