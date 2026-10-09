<?php

namespace Encore\Admin\Grid\Tools;

use Encore\Admin\Grid\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class TotalRow extends AbstractTool
{
    /**
     * @var Builder
     */
    protected $query;

    /**
     * @var array<mixed>
     */
    protected $columns;

    /**
     * TotalRow constructor.
     *
     * @param Builder|\Illuminate\Database\Eloquent\Builder<Model> $query
     * @param array<mixed>   $columns
     */
    public function __construct($query, array $columns)
    {
        /** @phpstan-ignore-next-line Property Encore\Admin\Grid\Tools\TotalRow::$query (Illuminate\Database\Query\Builder) does not accept Illuminate\Database\Eloquent\Builder<Illuminate\Database\Eloquent\Model>|Illuminate\Database\Query\Builder. */
        $this->query = $query;

        $this->columns = $columns;
    }

    /**
     * Get total value of current column.
     *
     * @param string $column
     * @param mixed  $display
     *
     * @return mixed
     */
    protected function total($column, $display = null)
    {
        if (!is_callable($display) && !is_null($display)) {
            return $display;
        }

        $sum = $this->query->sum($column);

        if (is_callable($display)) {
            return call_user_func($display, $sum);
        }

        return $sum;
    }

    /**
     * Render total-row.
     */
    public function render()
    {
        /** @phpstan-ignore-next-line Call to an undefined method Encore\Admin\Grid|Illuminate\Support\Collection<int|string, mixed>::flatMap(). */
        $columns = $this->getGrid()->visibleColumns()->flatMap(function (Column $column) {
            $name = $column->getName();

            $total = '';

            if (Arr::has($this->columns, $name)) {
                $total = $this->total($name, Arr::get($this->columns, $name));
            }

            return [$name => $total];
        })->toArray();

        return view('admin::grid.total-row', compact('columns'));
    }
}
