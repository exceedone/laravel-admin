<?php

namespace Encore\Admin\Form\Field;

class Url extends Text
{
    protected $customOptions = [];

    public function setCustomOptions(array $customOptions)
    {
        $this->customOptions = $customOptions;
        return $this;
    }

    public function getCustomOptions()
    {
        return $this->customOptions;
    }

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
