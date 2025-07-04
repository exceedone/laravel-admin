<?php

namespace Encore\Admin\Form\Field;

class Email extends Text
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

    protected $rules = 'nullable|email';

    public function render()
    {
        $this->prepend('<i class="fa fa-envelope fa-fw"></i>')
            ->defaultAttribute('type', 'email');

        return parent::render();
    }
}
