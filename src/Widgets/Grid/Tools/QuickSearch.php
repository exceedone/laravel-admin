<?php

namespace OpenAdmin\Admin\Widgets\Grid\Tools;

use OpenAdmin\Admin\Widgets\Grid\Grid;
use Illuminate\Support\Arr;

class QuickSearch extends AbstractTool
{
    /**
     * @var string
     */
    protected $view = 'admin::grid.quick-search';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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
