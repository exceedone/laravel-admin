<?php

namespace OpenAdmin\Admin\Form\Field;

use OpenAdmin\Admin\Form\Field;

class Nullable extends Field
{
    public function __construct()
    {
    }

    /**
     * @param mixed $method
     * @param mixed $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        return $this;
    }
}
