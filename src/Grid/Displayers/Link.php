<?php

namespace OpenAdmin\Admin\Grid\Displayers;

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

        return "<a href='$href' target='$target'>{$this->value}</a>";
    }
}
