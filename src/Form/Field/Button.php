<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;

class Button extends Field
{
    /**
     * @var string
     */
    protected $class = 'btn-primary';

    /**
     * @return $this
     */
    public function info()
    {
        $this->class = 'btn-info';

        return $this;
    }

    /**
     * @param string $event
     * @param string $callback
     * @return void
     */
    public function on($event, $callback)
    {
        /** @var string $selector */
        $selector = $this->getElementClassSelector();
        $this->script = <<<EOT

        $('{$selector}').on('$event', function() {
            $callback
        });

EOT;
    }
}
