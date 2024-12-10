<?php

namespace OpenAdmin\Admin\Layout;

interface Buildable
{
    /**
     * Build the element.
     *
     * @return mixed
     */
    public function build();
}
