<?php

namespace Encore\Admin\Grid\Concerns;

use Closure;
use Encore\Admin\Grid\Tools;

trait HasTools
{
    use HasQuickSearch;

    /**
     * Header tools.
     *
     * @var Tools
     */
    public $tools;

    /**
     * Setup grid tools.
     *
     * @return $this
     */
    protected function initTools()
    {
        $this->tools = new Tools($this);

        return $this;
    }

    /**
     * Disable header tools.
     *
     * @return $this
     */
    public function disableTools(bool $disable = true)
    {
        // @phpstan-ignore-next-line Return value is always $this(Encore\Admin\Grid) at runtime
        return $this->option('show_tools', !$disable);
    }

    /**
     * Setup grid tools.
     *
     * @param Closure $callback
     *
     * @return void
     */
    public function tools(Closure $callback)
    {
        call_user_func($callback, $this->tools);
    }

    /**
     * Render custom tools.
     * @param string $position
     *
     * @return string
     */
    public function renderHeaderTools($position = 'left')
    {
        return $this->tools->renderPosition($position);
    }

    /**
     * If grid show header tools.
     *
     * @return bool
     */
    public function showTools()
    {
        // @phpstan-ignore-next-line Return value is always bool at runtime
        return $this->option('show_tools');
    }
}
