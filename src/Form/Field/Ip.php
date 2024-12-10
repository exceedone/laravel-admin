<?php

namespace OpenAdmin\Admin\Form\Field;

class Ip extends Text
{
    /**
     * @var string
     */
    protected $rules = 'nullable|ip';

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
        'alias' => 'ip',
    ];

    /**
     * @return string
     */
    public function render()
    {
        $this->inputmask($this->options);

        $this->prepend('<i class="fa fa-laptop fa-fw"></i>')
            ->defaultAttribute('style', 'width: 130px');

        return parent::render();
    }
}
