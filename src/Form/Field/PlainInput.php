<?php

namespace Encore\Admin\Form\Field;

trait PlainInput
{
    /**
     * @var string|null
     */
    protected $prepend;

    /**
     * @var string|null
     */
    protected $append;

    /**
     * @param string $string
     * @return $this
     */
    public function prepend($string)
    {
        if (is_null($this->prepend)) {
            $this->prepend = $string;
        }

        return $this;
    }

    /**
     * @param string $string
     * @return $this
     */
    public function append($string)
    {
        if (is_null($this->append)) {
            $this->append = $string;
        }

        return $this;
    }

    /**
     * @return void
     */
    protected function initPlainInput()
    {
        if (empty($this->view)) {
            $this->view = 'admin::form.input';
        }
    }

    /**
     * @param mixed $attribute
     * @param mixed $value
     * @return $this
     */
    protected function defaultAttribute($attribute, $value)
    {
        // @phpstan-ignore-next-line $key is always int|string at runtime
        if (!array_key_exists($attribute, $this->attributes)) {
            // @phpstan-ignore-next-line $attribute is always array|string at runtime
            $this->attribute($attribute, $value);
        }

        return $this;
    }
}
