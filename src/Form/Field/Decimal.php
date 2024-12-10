<?php

namespace OpenAdmin\Admin\Form\Field;

class Decimal extends Text
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
     * @var array<string, string|bool>
     */
    protected $options = [
        'alias'      => 'decimal',
        'rightAlign' => true,
    ];

    /*
     * @return string
     */
    public function render()
    {
        $this->inputmask($this->options);

        $this->prepend('<i class="fa fa-terminal fa-fw"></i>')
            ->defaultAttribute('style', 'width: 130px');

        return parent::render();
    }
}
