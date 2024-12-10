<?php

namespace OpenAdmin\Admin\Form\Field;

class Time extends Date
{
    /**
     * @var string
     */
    protected $format = 'HH:mm:ss';

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|string
     */
    public function render()
    {
        $this->prepend('<i class="fa fa-clock-o fa-fw"></i>')
            ->defaultAttribute('style', 'width: 150px');

        return parent::render();
    }
}
