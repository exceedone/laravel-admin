<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;

class Textarea extends Field
{
    protected $customOptions = [];

    public function setCustomOptions(array $customOptions)
    {
        $this->customOptions = $customOptions;
        return $this;
    }

    public function getCustomOptions()
    {
        return $this->customOptions;
    }

    /**
     * Default rows of textarea.
     *
     * @var int
     */
    protected $rows = 5;

    /**
     * Set rows of textarea.
     *
     * @param int $rows
     *
     * @return $this
     */
    public function rows($rows = 5)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if (is_array($this->value)) {
            $this->value = json_encode($this->value, JSON_PRETTY_PRINT);
        }

        return parent::render()->with(['rows' => $this->rows]);
    }
}
