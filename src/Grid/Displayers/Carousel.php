<?php

namespace Encore\Admin\Grid\Displayers;

use Encore\Admin\Widgets\Carousel as CarouselWidget;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Storage;

class Carousel extends AbstractDisplayer
{
    /**
     * @param int $width
     * @param int $height
     * @param string $server
     * @return mixed
     */
    public function display(int $width = 300, int $height = 200, $server = '')
    {
        if ($this->value instanceof Arrayable) {
            $this->value = $this->value->toArray();
        }

        // @phpstan-ignore-next-line $array is always array<T> at runtime
        $this->value = array_values($this->value);

        if (empty($this->value)) {
            return '';
        }

        $images = collect((array) $this->value)->filter()->map(function ($path) use ($server) {
            // @phpstan-ignore-next-line $haystack is always string at runtime (and 1 more mixed-type assumption on this line)
            if (url()->isValidUrl($path) || strpos($path, 'data:image') === 0) {
                $image = $path;
            } elseif ($server) {
                // @phpstan-ignore-next-line $string is always string at runtime
                $image = rtrim($server, '/').'/'.ltrim($path, '/');
            } else {
                // @phpstan-ignore-next-line $name is always string|null at runtime (and 1 more mixed-type assumption on this line)
                $image = Storage::disk(config('admin.upload.disk'))->url($path);
            }

            $caption = '';

            return compact('image', 'caption');
        });

        // @phpstan-ignore-next-line $values is always bool|float|int|string|null at runtime (and 1 more mixed-type assumption on this line)
        $id = sprintf('carousel-%s-%s', $this->column->getName(), $this->getKey());

        return (new CarouselWidget($images))->width($width)->height($height)->id($id);
    }
}
