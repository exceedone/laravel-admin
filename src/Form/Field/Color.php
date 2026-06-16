<?php

namespace Encore\Admin\Form\Field;

class Color extends Text
{
    /**
     * @var array<string>
     */
    protected static $css = [
        '/vendor/laravel-admin/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.css',
    ];

    /**
     * @var array<string>
     */
    protected static $js = [
        '/vendor/laravel-admin/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.js',
    ];

    /**
     * Use `hex` format.
     *
     * @return $this
     */
    public function hex()
    {
        return $this->options(['format' => 'hex']);
    }

    /**
     * Use `rgb` format.
     *
     * @return $this
     */
    public function rgb()
    {
        return $this->options(['format' => 'rgb']);
    }

    /**
     * Use `rgba` format.
     *
     * @return $this
     */
    public function rgba()
    {
        return $this->options(['format' => 'rgba']);
    }

    /**
     * Render this filed.
     */
    public function render()
    {
        $options = json_encode($this->options);

        /** @phpstan-ignore-next-line Part $this->getElementClassSelector() (array|string) of encapsed string cannot be cast to string. */
        $this->script = "$('{$this->getElementClassSelector()}').parent().colorpicker($options);";

        $this->prepend('<i></i>')
            ->defaultAttribute('style', 'width: 140px');

        return parent::render();
    }
}
