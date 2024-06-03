<?php

namespace Encore\Admin\Widgets\Grid\Tools;

use Encore\Admin\Widgets\Grid\Grid;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class Paginator extends AbstractTool
{
    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator
     */
    protected $paginator = null;

    /**
     * Create a new Paginator instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid, LengthAwarePaginator $paginator)
    {
        $this->grid = $grid;

        $this->initPaginator($paginator);
    }


    /**
     * Initialize work for Paginator.
     *
     * @return void
     */
    protected function initPaginator(LengthAwarePaginator $paginator)
    {
        $this->paginator = $paginator;

        if ($this->paginator instanceof LengthAwarePaginator) {
            $this->paginator->appends(Request::all());
        }
    }

    /**
     * Get Pagination links.
     */
    protected function paginationLinks()
    {
        return $this->paginator->render('admin::pagination');
    }

    /**
     * Get per-page selector.
     *
     * @return PerPageSelector
     */
    protected function perPageSelector()
    {
        return new PerPageSelector($this->grid);
    }

    /**
     * Get range infomation of paginator.
     *
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected function paginationRanger()
    {
        $parameters = [
            'first' => $this->paginator->firstItem(),
            'last'  => $this->paginator->lastItem(),
            'total' => $this->paginator->total(),
        ];

        $parameters = collect($parameters)->flatMap(function ($parameter, $key) {
            return [$key => "<b>$parameter</b>"];
        });

        return trans('admin.pagination.range', $parameters->all());
    }

    /**
     * Render Paginator.
     *
     * @return string
     */
    public function render()
    {
        // if (!$this->grid->showPagination()) {
        //     return '';
        // }

        return $this->paginationRanger().
            $this->paginationLinks().
            $this->perPageSelector();
    }
}
