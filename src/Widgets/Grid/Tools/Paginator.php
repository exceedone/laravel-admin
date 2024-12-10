<?php

namespace OpenAdmin\Admin\Widgets\Grid\Tools;

use OpenAdmin\Admin\Widgets\Grid\Grid;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class Paginator extends AbstractTool
{
    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<array<string, mixed>>
     */
    protected $paginator = null;

    /**
     * Create a new Paginator instance.
     *
     * @param Grid $grid
     * @param \Illuminate\Pagination\LengthAwarePaginator<array<string, mixed>> $paginator
     */
    public function __construct(Grid $grid, LengthAwarePaginator $paginator)
    {
        $this->grid = $grid;

        $this->initPaginator($paginator);
    }


    /**
     * Initialize work for Paginator.
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator<array<string, mixed>> $paginator
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
     *
     * @return \Illuminate\Contracts\Support\Htmlable
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

        /** @phpstan-ignore-next-line Binary operation "." between string|Symfony\Contracts\Translation\TranslatorInterface and Illuminate\Contracts\Support\Htmlable results in an error. */
        return $this->paginationRanger().
            $this->paginationLinks().
            $this->perPageSelector();
    }
}
