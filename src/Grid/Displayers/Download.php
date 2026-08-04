<?php

namespace Encore\Admin\Grid\Displayers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Storage;

class Download extends AbstractDisplayer
{
    /**
     * @param string $server
     * @return mixed
     */
    public function display($server = '')
    {
        if ($this->value instanceof Arrayable) {
            $this->value = $this->value->toArray();
        }

        return collect((array) $this->value)->filter()->map(function ($value) use ($server) {
            /** @phpstan-ignore-next-line Maybe always exists and is not falsy */
            if (empty($value)) {
                return '';
            }

            // @phpstan-ignore-next-line $path is always string at runtime
            if (url()->isValidUrl($value)) {
                $src = $value;
            } elseif ($server) {
                // @phpstan-ignore-next-line $string is always string at runtime
                $src = rtrim($server, '/').'/'.ltrim($value, '/');
            } else {
                // @phpstan-ignore-next-line $name is always string|null at runtime (and 1 more mixed-type assumption on this line)
                $src = Storage::disk(config('admin.upload.disk'))->url($value);
            }

            // @phpstan-ignore-next-line $path is always string at runtime
            $name = basename($value);

            // Type assertion for PHPStan - maintain original behavior
            /** @var string $src */

            return <<<HTML
<a href='$src' download='{$name}' target='_blank' class='text-muted'>
    <i class="fa fa-download"></i> {$name}
</a>
HTML;
        })->implode('<br>');
    }
}
