<?php

namespace OpenAdmin\Admin\Grid\Tools;

use OpenAdmin\Admin\Grid;
use Illuminate\Contracts\Support\Renderable;

abstract class AbstractTool implements Renderable
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var bool
     */
    protected $disabled = false;

    /**
     * Toggle this button.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disable(bool $disable = true)
    {
        $this->disabled = $disable;

        return $this;
    }

    /**
     * If the tool is allowed.
     *
     * @return bool
     */
    public function allowed()
    {
        return !$this->disabled;
    }

    /**
     * Set parent grid.
     *
     * @param Grid $grid
     *
     * @return $this
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    abstract public function render();

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
