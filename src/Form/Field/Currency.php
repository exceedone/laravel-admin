<?php

namespace Encore\Admin\Form\Field;

class Currency extends Text
{
    /**
     * @var string
     */
    protected $symbol = '$';

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
        'alias'              => 'currency',
        'radixPoint'         => '.',
        'prefix'             => '',
        'removeMaskOnSubmit' => true,
    ];

    /**
     * Set symbol for currency field.
     *
     * @param string $symbol
     *
     * @return $this
     */
    public function symbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * Set digits for input number.
     *
     * @param int $digits
     *
     * @return $this
     */
    public function digits($digits)
    {
        return $this->options(compact('digits'));
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($value)
    {
        return (float) $value;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function render()
    {
        $this->inputmask($this->options);

        $this->prepend($this->symbol)
            ->defaultAttribute('style', 'width: 120px');

        return parent::render();
    }
}
