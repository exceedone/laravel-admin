<?php

namespace OpenAdmin\Admin\Grid\Filter;

class Hidden extends AbstractFilter
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * Hidden constructor.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;

        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @param array<mixed> $inputs
     *
     * @return void
     */
    public function condition($inputs)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return "<input type='hidden' name='$this->name' value='$this->value'>";
    }
}
