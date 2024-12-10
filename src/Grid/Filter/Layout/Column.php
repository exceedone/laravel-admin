<?php

namespace OpenAdmin\Admin\Grid\Filter\Layout;

use OpenAdmin\Admin\Grid\Filter\AbstractFilter;
use Illuminate\Support\Collection;

class Column
{
    /**
     * @var Collection<int|string, mixed>
     */
    protected $filters;

    /**
     * @var int
     */
    protected $width;

    /**
     * Column constructor.
     *
     * @param int $width
     */
    public function __construct($width = 12)
    {
        $this->width = $width;
        $this->filters = new Collection();
    }

    /**
     * Add a filter to this column.
     *
     * @param AbstractFilter $filter
     *
     * @return void
     */
    public function addFilter(AbstractFilter $filter)
    {
        $this->filters->push($filter);
    }

    /**
     * Get all filters in this column.
     *
     * @return Collection<int|string, mixed>
     */
    public function filters()
    {
        return $this->filters;
    }

    /**
     * Set column width.
     *
     * @param int $width
     *
     * @return void
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Get column width.
     *
     * @return int
     */
    public function width()
    {
        return $this->width;
    }

    /**
     * Remove filter from column by id.
     * @param string|int $id
     *
     * @return void
     */
    public function removeFilterByID($id)
    {
        $this->filters = $this->filters->reject(function (AbstractFilter $filter) use ($id) {
            return $filter->getId() == $id;
        });
    }
}
