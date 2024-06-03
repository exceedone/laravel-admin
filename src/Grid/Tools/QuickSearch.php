<?php

namespace Encore\Admin\Grid\Tools;

use Encore\Admin\Grid;
use Illuminate\Support\Arr;

class QuickSearch extends AbstractTool
{
    /**
     * @var string
     */
    protected $view = 'admin::grid.quick-search';

    public function render()
    {
        $query = request()->query();

        Arr::forget($query, Grid::getSearchKey());

        $vars = [
            'action' => request()->url().'?'.http_build_query($query),
            'key'    => Grid::getSearchKey(),
            'value'  => request(Grid::getSearchKey()),
        ];

        return view($this->view, $vars);
    }
}
