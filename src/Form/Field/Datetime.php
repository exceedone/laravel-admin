<?php

namespace OpenAdmin\Admin\Form\Field;

class Datetime extends Date
{
    /**
     * @var string
     */
    protected $format = 'YYYY-MM-DD HH:mm:ss';

    /**
     * @return string
     */
    public function render()
    {
        $this->defaultAttribute('style', 'width: 160px');

        return parent::render();
    }
}
