<?php

namespace Encore\Admin\Grid\Displayers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class Label extends AbstractDisplayer
{
    /**
     * @param string|Arrayable<int|string, mixed>|array<mixed> $style
     * @return mixed|string
     */
    public function display($style = 'success')
    {
        if ($this->value instanceof Arrayable) {
            $this->value = $this->value->toArray();
        }

        return collect((array) $this->value)->map(function ($item) use ($style) {
            if (is_array($style)) {
                // @phpstan-ignore-next-line $key is always int|string|null at runtime
                $style = Arr::get($style, $this->getColumn()->getOriginal(), 'success');
            }

            // @phpstan-ignore-next-line $item is always castable to string at runtime (and 1 more mixed-type assumption on this line)
            return "<span class='label label-{$style}'>$item</span>";
        })->implode('&nbsp;');
    }
}
