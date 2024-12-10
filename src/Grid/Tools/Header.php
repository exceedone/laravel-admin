<?php

namespace OpenAdmin\Admin\Grid\Tools;

use OpenAdmin\Admin\Grid;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class Header extends AbstractTool
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder<Model>|null
     */
    protected $queryBuilder;

    /**
     * Header constructor.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Get model query builder
     *
     * @return \Illuminate\Database\Eloquent\Builder<Model>
     */
    public function queryBuilder()
    {
        if (!$this->queryBuilder) {
            $this->queryBuilder = $this->grid->model()->getQueryBuilder();
        }

        return $this->queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $content = call_user_func($this->grid->header(), $this->queryBuilder());

        if (empty($content)) {
            return '';
        }

        if ($content instanceof Renderable) {
            $content = $content->render();
        }

        if ($content instanceof Htmlable) {
            $content = $content->toHtml();
        }

        return <<<HTML
    <div class="box-header with-border clearfix">
        {$content}
    </div>
HTML;
    }
}
