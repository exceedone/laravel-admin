<?php

namespace Encore\Admin\Widgets\Grid;

use Encore\Admin\Widgets\Grid\Grid;
use Encore\Admin\Widgets\Grid\Tools\AbstractTool;
use Encore\Admin\Widgets\Grid\Tools\BatchActions;
//use Encore\Admin\Widgets\Grid\Tools\FilterButton;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class Tools implements Renderable
{
    protected const POSITIONS = ['left', 'right'];

    public static $defaultPosition = 'left';

    /**
     * Parent grid.
     *
     * @var Grid
     */
    protected $grid;

    /**
     * Collection of tools.
     */
    protected $tools;

    /**
     * Create a new Tools instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;

        $this->tools = [
            'left' => new Collection(),
            'right' =>new Collection()
        ];

        $this->appendDefaultTools();
    }

    /**
     * Append default tools.
     */
    protected function appendDefaultTools()
    {
        $this->append(new BatchActions(), 'left')
            //->append(new FilterButton(), 'left')
            ;
    }

    /**
     * Append tools.
     *
     * @param AbstractTool|string $tool
     *
     * @return $this
     */
    public function append($tool, $position = null)
    {
        $position = $this->getPosition($position);

        $this->tools[$position]->push($tool);

        return $this;
    }

    /**
     * Prepend a tool.
     *
     * @param AbstractTool|string $tool
     *
     * @return $this
     */
    public function prepend($tool, $position = null)
    {
        $position = $this->getPosition($position);

        $this->tools[$position]->prepend($tool);

        return $this;
    }

    protected function getPosition($position){
        if(is_null($position)){
            return static::$defaultPosition;
        }

        if(!in_array($position, static::POSITIONS)){
            return 'left';
        }
        return $position;
    }

    /**
     * Disable filter button.
     *
     * @return void
     */
    public function disableFilterButton(bool $disable = true)
    {
        foreach(static::POSITIONS as $position){
            $this->tools[$position] = $this->tools[$position]->map(function ($tool) {
                return $tool;
            });
        }

    }

    /**
     * Disable refresh button.
     *
     * @return void
     *
     * @deprecated
     */
    public function disableRefreshButton(bool $disable = true)
    {
        //
    }

    /**
     * Disable batch actions.
     *
     * @return void
     */
    public function disableBatchActions(bool $disable = true)
    {
        foreach (static::POSITIONS as $position) {
            $this->tools[$position] = $this->tools[$position]->map(function ($tool) use ($disable) {
                if ($tool instanceof BatchActions) {
                    return $tool->disable($disable);
                }

                return $tool;
            });
        }
    }

    /**
     * @param \Closure $closure
     */
    public function batch(\Closure $closure)
    {
        foreach (static::POSITIONS as $position) {
            $batch = $this->tools[$position]->first(function ($tool) {
                return $tool instanceof BatchActions;
            });
            
            if(is_null($batch)){
                return;
            }

            call_user_func($closure, $batch);
        }
    }

    /**
     * Render header tools bar.
     *
     * @return string
     */
    public function render()
    {
        return view('admin.grid.tools', [
            'left' => $this->renderPosition('left'),
            'right' => $this->renderPosition('right'),
        ]);
    }
    
    /**
     * Render header tools bar (select position).
     *
     * @return string
     */
    public function renderPosition($position)
    {
        $position = $this->getPosition($position);

        return $this->tools[$position]->map(function ($tool) {
            if ($tool instanceof AbstractTool) {
                if (!$tool->allowed()) {
                    return '';
                }

                $tool = $tool->setGrid($this->grid)->render();
            }

            if ($tool instanceof Renderable) {
                return $tool->render();
            }

            if ($tool instanceof Htmlable) {
                return $tool->toHtml();
            }

            return (string) $tool;
        })->implode(' ');
    }
}
