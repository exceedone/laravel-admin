<?php

namespace Encore\Admin\Grid\Filter\Presenter;

use Encore\Admin\Facades\Admin;

class Checkbox extends Radio
{
    protected function prepare()
    {
        /** @phpstan-ignore-next-line Part $this->filter->getId() (array|string) of encapsed string cannot be cast to string. */
        $script = "$('.{$this->filter->getId()}').iCheck({checkboxClass:'icheckbox_minimal-blue'});";

        Admin::script($script);
    }
}
