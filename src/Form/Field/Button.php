<?php

namespace OpenAdmin\Admin\Form\Field;

use OpenAdmin\Admin\Form\Field;

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
        $this->class = 'btn-twitter';

        return $this;
    }

    /**
     * @param string $event
     * @param string $callback
     * @return void
     */
    public function on($event, $callback)
    {
        $this->script = <<<EOT

        $('{$this->getElementClassSelector()}').on('$event', function() {
            $callback
        });

EOT;
    }
}
