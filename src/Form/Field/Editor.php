<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;

class Editor extends Field
{
    protected static $js = [
        '//cdn.ckeditor.com/4.5.10/standard/ckeditor.js',
    ];

    public function render()
    {
        /** @phpstan-ignore-next-line Part \$this->id (array|string) of encapsed string cannot be cast to string. */
        $this->script = "CKEDITOR.replace('{$this->id}');";

        return parent::render();
    }
}
