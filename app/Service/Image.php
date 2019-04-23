<?php

namespace App\Service;

use Intervention\Image\Gd\Font;
use Intervention\Image\ImageManager;

class Image
{
    const LINE_HEIGHT = 1.25;

    /** @var ImageManager */
    protected $manager;
    /** @var string текст для изображения  */
    protected $text;
    /** @var integer количество строк текста */
    protected $count;
    /** @var array image config */
    protected $settings;

    /**
     * Image constructor.
     * @param string $text текст для изображения
     */
    public function __construct($text)
    {
        $this->settings = config('image');
        $this->manager = new ImageManager();
        $this->text = $text;
    }

    /**
     * Метод удаления изображения
     * @param string $path путь к файлу
     */
    public static function remove($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Метод формирования изображения для отправки
     * @return \Illuminate\Support\Collection
     */
    public function createImage()
    {
        $image = $this->manager->make(env('IMG_DEBUG'));

        $this->breakString($image->getWidth() - $this->settings['padding'] * 2);

        $textLayer = $this->manager
            ->canvas(
                $image->getWidth(),
                $this->getCanvasHeight(),
                $this->settings['bg_color']
            )
            ->text($this->text, $this->settings['padding'], $this->settings['padding'], function(Font $font) {
                $font->file(base_path($this->settings['font']));
                $font->size($this->settings['font_size']);
                $font->color($this->settings['font_color']);
                $font->valign('top');
           });

        $image->insert($textLayer)->save($this->getSavePath());

        $result = collect([
            'base' => realpath($image->basePath()),
            'mime' => mime_content_type($image->basePath()),
            'name' => $image->basename,
            'size' => $image->filesize()
        ]);

        return $result;
    }

    /**
     * Метод разбивает текст на строки входяшие в пределы изображения
     * @param integer $width максимальная ширина блока с текстом
     */
    private function breakString($width)
    {
        $textWidth = $this->getBoxWidth($this->text);
        $lines = [];


        if ($textWidth > $width) {
            $words = explode(' ', $this->text);

            $line = [];

            foreach ($words as $word) {
                $lineWidth = $this->getBoxWidth(implode(' ', $line).' '.$word);

                if ($lineWidth > $width) {
                    $lines[] = implode(' ', $line);
                    $line = [];
                }

                $line[] = $word;
            }
            $lines[] = implode(' ', $line);
        }

        $this->text  = $lines ? implode("\n", $lines) : $this->text;
        $this->count = $lines ? count($lines) : 1;
    }

    /**
     * Метод расчета высоты блока фона для текста
     * @return float|int
     */
    private function getCanvasHeight()
    {
        return self::LINE_HEIGHT * $this->settings['font_size'] * $this->count + $this->settings['padding'];
    }

    /**
     * Метод расчета ширины блока под строку текста
     * @param string $text строка текста для расчета
     * @return float|int ширина блока
     */
    private function getBoxWidth($text)
    {
        $box = imagettfbbox(
            0.75 * $this->settings['font_size'],
            0,
            base_path($this->settings['font']),
            $text
        );

        return abs($box[4] - $box[0]);
    }

    /**
     * Метод генерирует путь к файлу для сохранения
     * @return string
     */
    private function getSavePath()
    {
        return base_path($this->settings['save_folder'].'/'.time().'.jpg');
    }
}
