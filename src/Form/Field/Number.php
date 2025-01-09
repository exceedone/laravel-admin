<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Validator\DigitBetweenRule;
use Encore\Admin\Validator\DigitMinRule;
use Encore\Admin\Validator\DigitMaxRule;

class Number extends Text
{
    protected $rules = ['nullable', 'numeric'];

    protected static $js = [
        '/vendor/open-admin/number-input/bootstrap-number-input.js',
    ];

    public function render()
    {
        $this->default($this->default);

        $this->script = <<<EOT

$('{$this->getElementClassSelector()}:not(.initialized)')
    .addClass('initialized')
    .bootstrapNumber({
        upClass: 'success',
        downClass: 'primary',
        center: true
    });

EOT;

        $this->prepend('')->defaultAttribute('style', 'width: 100px');

        return parent::render();
    }

    /**
     * Set min value of number field.
     *
     * @param int $value
     *
     * @return $this
     */
    public function min($value)
    {
        $this->attribute('min', $value);

        $this->rules([new DigitMinRule($value)]);

        return $this;
    }

    /**
     * Set max value of number field.
     *
     * @param int $value
     *
     * @return $this
     */
    public function max($value)
    {
        $this->attribute('max', $value);

        $this->rules([new DigitMaxRule($value)]);

        return $this;
    }

    

    /**
     * Set min and max value of number field. 
     * *And set validation.*
     *
     * @param int $min
     * @param int $max
     *
     * @return $this
     */
    public function between($min, $max)
    {
        $this->attribute('min', $min);
        $this->attribute('max', $max);

        $this->rules([new DigitBetweenRule($min,$max)]);

        return $this;
    }

    
}
