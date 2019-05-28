<?php

namespace App\Service;

use Intervention\Image\Gd\Font;
use Intervention\Image\ImageManager;

class Image
{
    const LINE_HEIGHT = 1.25;

    /** @var ImageManager */
    protected $manager;
    /** @var \Intervention\Image\Image */
    protected $image;
    /** @var string текст для изображения */
    protected $text;
    /** @var int количество строк текста */
    protected $lines;
    /** @var array image config */
    protected $settings;

    /**
     * Image constructor.
     */
    public function __construct()
    {
        $this->settings = config('image');
        $this->manager  = new ImageManager();
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
     * Возвращает коллекцию данных об изображении
     * @return \Illuminate\Support\Collection
     */
    public function getImage()
    {
        return collect([
            'base' => realpath($this->image->basePath()),
            'mime' => mime_content_type($this->image->basePath()),
            'name' => $this->image->basename,
            'size' => $this->image->filesize(),
        ]);
    }

    /**
     * Изменение изображения
     * @param \Intervention\Image\Image $image
     * @return $this
     */
    protected function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string текст накладываемый на изображение
     */
    protected function getText()
    {
        return $this->text;
    }

    /**
     * Изменение текста накладываемого на изображение
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Изменение количества строк текста
     * @param int $lines
     * @return $this
     */
    protected function setLines($lines)
    {
        $this->lines = $lines;
        return $this;
    }

    /**
     * @return int количество строк текста
     */
    protected function getLines()
    {
        return $this->lines;
    }

    /**
     * Метод формирования изображения для отправки
     * @return $this
     */
    public function createImage()
    {
        $image = $this->makeImage();

        if ($this->getText()) {
            $this->breakString($image->getWidth() - $this->settings['padding'] * 2);

            $textLayer = $this->manager
                ->canvas(
                    $image->getWidth(),
                    $this->getCanvasHeight(),
                    $this->settings['bg_color']
                )
                ->text($this->getText(), $this->settings['padding'], $this->settings['padding'], function (Font $font) {
                    $font->file(base_path($this->settings['font']));
                    $font->size($this->settings['font_size']);
                    $font->color($this->settings['font_color']);
                    $font->valign('top');
                });

            $image->insert($textLayer);
        }

        $image->save($this->getSavePath());

        $this->setImage($image);

        return $this;
    }

    /**
     * Метод создания объекта из исходного изображения
     * @return \Intervention\Image\Image
     */
    protected function makeImage()
    {
        // TODO: получение актуального изображения
        return $this->manager->make(env('IMG_DEBUG'));
    }

    /**
     * Метод разбивает текст на строки входяшие в пределы изображения
     * @param integer $width максимальная ширина блока с текстом
     */
    private function breakString($width)
    {
        $text = $this->getText();

        $textWidth = $this->calculateBoxWidth($text);
        $lines     = [];

        if ($textWidth > $width) {
            $words = explode(' ', $text);

            $line = [];

            foreach ($words as $word) {
                $lineWidth = $this->calculateBoxWidth(implode(' ', $line).' '.$word);

                if ($lineWidth > $width) {
                    $lines[] = implode(' ', $line);
                    $line    = [];
                }

                $line[] = $word;
            }
            $lines[] = implode(' ', $line);
        }

        $this->setText(
            $lines ? implode("\n", $lines) : $text
        );

        $this->setLines(
            $lines ? count($lines) : 1
        );
    }

    /**
     * Метод расчета высоты блока фона для текста
     * @return float|int
     */
    private function getCanvasHeight()
    {
        return self::LINE_HEIGHT * $this->settings['font_size'] * $this->getLines() + $this->settings['padding'];
    }

    /**
     * Метод расчета ширины блока под строку текста
     * @param string $text строка текста для расчета
     * @return int ширина блока
     */
    private function calculateBoxWidth($text)
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
