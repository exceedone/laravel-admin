<?php

namespace Encore\Admin\Grid\Displayers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Storage;

class Image extends AbstractDisplayer
{
    /**
     * @param string $server
     * @param int $width
     * @param int $height
     * @return mixed
     */
    public function display($server = '', $width = 200, $height = 200)
    {
        if ($this->value instanceof Arrayable) {
            $this->value = $this->value->toArray();
        }

        return collect((array) $this->value)->filter()->map(function ($path) use ($server, $width, $height) {
            // @phpstan-ignore-next-line $haystack is always string at runtime (and 1 more mixed-type assumption on this line)
            if (url()->isValidUrl($path) || strpos($path, 'data:image') === 0) {
                $src = $path;
            } elseif ($server) {
                // @phpstan-ignore-next-line $string is always string at runtime
                $src = rtrim($server, '/').'/'.ltrim($path, '/');
            } else {
                // @phpstan-ignore-next-line $name is always string|null at runtime (and 1 more mixed-type assumption on this line)
                $src = Storage::disk(config('admin.upload.disk'))->url($path);
            }

            // @phpstan-ignore-next-line $src is always castable to string at runtime
            return "<img src='$src' style='max-width:{$width}px;max-height:{$height}px' class='img img-thumbnail' />";
        })->implode('&nbsp;');
    }
}
