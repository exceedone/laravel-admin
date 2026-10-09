<?php

namespace Encore\Admin\Grid\Displayers;

class Button extends AbstractDisplayer
{
    /**
     * @param string|null $style
     *
     * @return string
     */
    public function display($style = null)
    {
        $style = collect((array) $style)->map(function ($style) {
            return 'btn-'.$style;
        })->implode(' ');

        // @phpstan-ignore-next-line $this->value is always castable to string at runtime
        return "<span class='btn $style'>{$this->value}</span>";
    }
}
