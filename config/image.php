<?php

return [
    'driver'      => 'gd',
    'save_folder' => env('IMG_SAVE_FOLDER', 'storage/app/image'),
    'bg_color'    => env('TEXT_BG_COLOR') ? explode(',', env('TEXT_BG_COLOR')) : null,
    'font'        => env('TEXT_FONT', 'resources/fonts/Roboto-Regular.ttf'),
    'font_size'   => env('TEXT_FONT_SIZE', 16),
    'font_color'  => env('TEXT_FONT_COLOR', '#fff'),
    'padding'     => env('TEXT_PADDING', 0),
];
