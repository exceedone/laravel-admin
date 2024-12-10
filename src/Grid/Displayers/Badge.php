<?php

namespace OpenAdmin\Admin\Grid\Displayers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class Badge extends AbstractDisplayer
{
    /**
     * @param string|Arrayable<int|string, mixed>|array<int|string, mixed> $style
     * @return mixed|string
     */
    public function display($style = 'red')
    {
        if ($this->value instanceof Arrayable) {
            $this->value = $this->value->toArray();
        }

        return collect((array) $this->value)->map(function ($name) use ($style) {
            if (is_array($style)) {
                $style = Arr::get($style, $this->getColumn()->getOriginal(), 'red');
            }

            return "<span class='badge bg-{$style}'>$name</span>";
        })->implode('&nbsp;');
    }
}
