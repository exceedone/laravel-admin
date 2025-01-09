<?php

namespace Encore\Admin\Form\Field;

class Mobile extends Text
{
    /**
     * @var array<string>
     */
    protected static $js = [
        '/vendor/open-admin/AdminLTE/plugins/input-mask/jquery.inputmask.bundle.min.js',
    ];

    /**
     * @see https://github.com/RobinHerbots/Inputmask#options
     *
     * @var array<string, string>
     */
    protected $options = [
        'mask' => '99999999999',
    ];

    /**
     * @return string
     */
    public function render()
    {
        $this->inputmask($this->options);

        $this->prepend('<i class="fa fa-phone fa-fw"></i>')
            ->defaultAttribute('style', 'width: 150px');

        return parent::render();
    }
}
