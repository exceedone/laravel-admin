<?php

namespace Encore\Admin\Form\Field;

class Url extends Text
{
    use HasCustomOptions;

    /**
     * @var string
     */
    protected $rules = 'nullable|url';

    /**
     * {@inheritdoc}
     * @return string
     */
    public function render()
    {
        $this->prepend('<i class="fa fa-internet-explorer fa-fw"></i>')
            ->defaultAttribute('type', 'url');

        return parent::render();
    }
}
