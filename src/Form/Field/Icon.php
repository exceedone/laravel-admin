<?php

namespace OpenAdmin\Admin\Form\Field;

class Icon extends Text
{
    /**
     * @var string
     */
    protected $default = 'fa-pencil';

    /**
     * @var array<string>
     */
    protected static $css = [
        '/vendor/open-admin/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css',
    ];

    /**
     * @var array<string>
     */
    protected static $js = [
        '/vendor/open-admin/fontawesome-iconpicker/dist/js/fontawesome-iconpicker-customize.js',
    ];

    /**
     * @return string
     */
    public function render()
    {
        $this->script = <<<EOT

$('{$this->getElementClassSelector()}').iconpicker({placement:'bottomLeft'});

EOT;

        $this->prepend('<i class="fa fa-pencil fa-fw"></i>')
            ->defaultAttribute('style', 'width: 140px');

        return parent::render();
    }
}
