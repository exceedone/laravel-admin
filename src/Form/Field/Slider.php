<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;

class Slider extends Field
{
    /**
     * @var array<string>
     */
    protected static $css = [
        '/vendor/open-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.css',
        '/vendor/open-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.skinNice.css',
    ];

    /**
     * @var array<string>
     */
    protected static $js = [
        '/vendor/open-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.min.js',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $options = [
        'type'     => 'single',
        'prettify' => false,
        'hasGrid'  => true,
    ];

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|string
     */
    public function render()
    {
        $option = json_encode($this->options);

        $this->script = "$('{$this->getElementClassSelector()}').ionRangeSlider($option)";

        return parent::render();
    }
}
