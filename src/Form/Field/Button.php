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
        /** @phpstan-ignore-next-line argument.type */
        $this->script = <<<EOT

        $('{$this->getElementClassSelector()}').on('$event', function() {
            $callback
        });

EOT;
    }
}
