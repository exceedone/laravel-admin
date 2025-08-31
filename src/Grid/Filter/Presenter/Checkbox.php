<?php

namespace Encore\Admin\Grid\Filter\Presenter;

use Encore\Admin\Facades\Admin;

class Checkbox extends Radio
{
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\Presenter\Checkbox::prepare() has no return type specified. */
    protected function prepare()
    {
        $script = "$('.{$this->filter->getId()}').iCheck({checkboxClass:'icheckbox_minimal-blue'});"; /** @phpstan-ignore-next-line Method Encore\Admin\Facades\Admin::script() has no return type specified. */

        Admin::script($script);
    }
}
