<?php

namespace Encore\Admin\Grid\Displayers;

class Link extends AbstractDisplayer
{
    /**
     * @param string $href
     * @param string $target
     *
     * @return string
     */
    public function display($href = '', $target = '_blank')
    {
        $href = $href ?: $this->value;

        // @phpstan-ignore-next-line $href is always castable to string at runtime (and 1 more mixed-type assumption on this line)
        return "<a href='$href' target='$target'>{$this->value}</a>";
    }
}
