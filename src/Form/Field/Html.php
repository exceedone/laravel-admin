<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;
use Illuminate\Support\Arr;

class Html extends Field
{
    /**
     * Htmlable.
     *
     * @var string|\Closure
     */
    protected $html = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var bool
     */
    protected $plain = false;

    /**
     * Create a new Html instance.
     *
     * @param mixed $html
     * @param array<mixed> $arguments
     */
    public function __construct($html, $arguments)
    {
        // @phpstan-ignore-next-line Assigned value is always Closure|string at runtime
        $this->html = $html;

        // @phpstan-ignore-next-line Assigned value is always string at runtime
        $this->label = Arr::get($arguments, 0);
    }

    /**
     * @return $this
     */
    public function plain()
    {
        $this->plain = true;

        return $this;
    }

    /**
     * Render html field.
     *
     * @return string
     */
    public function render()
    {
        if ($this->html instanceof \Closure) {
            // @phpstan-ignore-next-line Form and Model are guaranteed to be set during render
            $this->html = $this->html->call($this->form->model(), $this->form);
        }

        if ($this->plain) {
            // @phpstan-ignore-next-line Return value is always string at runtime
            return $this->html;
        }

        $viewClass = $this->getViewElementClasses();

        // Type assertion for PHPStan - maintain original behavior
        /** @var string $html */
        $html = $this->html;

        return <<<EOT
<div class="form-group">
    <label  class="{$viewClass['label']} control-label">{$this->label}</label>
    <div class="{$viewClass['field']}">
        {$html}
    </div>
</div>
EOT;
    }
}
