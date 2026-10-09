<?php

namespace Encore\Admin\Widgets\Grid\Concerns;

use Encore\Admin\Widgets\Grid\Grid;
use Encore\Admin\Widgets\Grid\Column;
use Encore\Admin\Widgets\Grid\Tools\ColumnSelector;
use Illuminate\Support\Collection;

trait CanHidesColumns
{
    /**
     * Default columns be hidden.
     *
     * @var array<mixed>
     */
    public $hiddenColumns = [];

    /**
     * Remove column selector on grid.
     *
     * @param bool $disable
     *
     * @return Grid|mixed
     */
    public function disableColumnSelector(bool $disable = true)
    {
        return $this->option('show_column_selector', !$disable);
    }

    /**
     * @return bool
     */
    public function showColumnSelector()
    {
        // @phpstan-ignore-next-line Return value is always bool at runtime
        return $this->option('show_column_selector');
    }

    /**
     * @return string
     */
    public function renderColumnSelector()
    {
        return (new ColumnSelector($this))->render();
    }

    /**
     * Setting default shown columns on grid.
     *
     * @param array<mixed>|string $columns
     *
     * @return $this
     */
    public function hideColumns($columns)
    {
        if (func_num_args()) {
            $columns = (array) $columns;
        } else {
            $columns = func_get_args();
        }

        $this->hiddenColumns = array_merge($this->hiddenColumns, $columns);

        return $this;
    }

    /**
     * Get visible columns from request query.
     *
     * @return array<mixed>
     */
    protected function getVisibleColumnsFromQuery()
    {
        // @phpstan-ignore-next-line $string is always string at runtime
        $columns = explode(',', request(ColumnSelector::SELECT_COLUMN_NAME));

        return array_filter($columns) ?:
            array_values(array_diff($this->columnNames, $this->hiddenColumns));
    }

    /**
     * Get all visible column instances.
     *
     * @return Collection<int|string, Column>|static
     */
    public function visibleColumns()
    {
        $visible = $this->getVisibleColumnsFromQuery();

        if (empty($visible)) {
            return $this->columns;
        }

        array_push($visible, Column::SELECT_COLUMN_NAME, Column::ACTION_COLUMN_NAME);

        return $this->columns->filter(function (Column $column) use ($visible) {
            return in_array($column->getName(), $visible);
        });
    }

    /**
     * Get all visible column names.
     *
     * @return array<mixed>
     */
    public function visibleColumnNames()
    {
        $visible = $this->getVisibleColumnsFromQuery();

        if (empty($visible)) {
            return $this->columnNames;
        }

        array_push($visible, Column::SELECT_COLUMN_NAME, Column::ACTION_COLUMN_NAME);

        return collect($this->columnNames)->filter(function ($column) use ($visible) {
            return in_array($column, $visible);
        })->toArray();
    }

    /**
     * Get default visible column names.
     *
     * @return array<mixed>
     */
    public function getDefaultVisibleColumnNames()
    {
        return array_values(
            array_diff(
                $this->columnNames,
                $this->hiddenColumns,
                [Column::SELECT_COLUMN_NAME, Column::ACTION_COLUMN_NAME]
            )
        );
    }
}
