<?php

namespace Encore\Admin\Form\Field;

trait HasCustomOptions
{
    /** @var array<string, mixed> */
    protected $customOptions = [];

    /**
     * @param array<string, mixed> $customOptions
     * @return static
     */
    public function setCustomOptions(array $customOptions)
    {
        $this->customOptions = $customOptions;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCustomOptions()
    {
        return $this->customOptions;
    }
}
