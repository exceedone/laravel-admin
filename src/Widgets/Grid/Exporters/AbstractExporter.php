<?php

namespace OpenAdmin\Admin\Widgets\Grid\Exporters;

use OpenAdmin\Admin\Widgets\Grid\Exporter;
use OpenAdmin\Admin\Widgets\Grid\Grid;

abstract class AbstractExporter implements ExporterInterface
{
    /**
     * @var \OpenAdmin\Admin\Widgets\Grid\Grid
     */
    protected $grid;

    /**
     * @var int
     */
    protected $page;

    /**
     * Create a new exporter instance.
     *
     * @param Grid|null $grid
     */
    public function __construct(Grid $grid = null)
    {
        if ($grid) {
            $this->setGrid($grid);
        }
    }

    /**
     * Set grid for exporter.
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
     * Get table of grid.
     *
     * @return string
     */
    public function getTable()
    {
        //ToDO:修正
        return 'ToDO_修正';
    }

    /**
     * Get data with export query.
     *
     * @param bool $toArray
     *
     * @return array|\Illuminate\Support\Collection|mixed
     */
    public function getData($toArray = true)
    {
        //ToDo: If support filter, set getfilter.
        //return $this->grid->getFilter()->execute($toArray);
        /** @phpstan-ignore-next-line use magic method */
        return $this->grid->execute($toArray);
    }

    /**
     * @param callable $callback
     * @param int      $count
     *
     * @return void
     */
    public function chunk(callable $callback, $count = 100)
    {
        //ToDo: If support filter, set getfilter.
        //return $this->grid->getFilter()->chunk($callback, $count);
        $this->grid->chunk($callback, $count);
    }

    /**
     * @return \Illuminate\Support\Collection<int|string, mixed>
     */
    public function getCollection()
    {
        /** @phpstan-ignore-next-line Unable to resolve the template type TKey in call to function collect */
        return collect($this->getData());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function getQuery()
    {
        // Export data of giving page number.
        if ($this->page) {
            $keyName = $this->grid->getKeyName();
            /** @phpstan-ignore-next-line maybe error */
            $perPage = request($model->getPerPageName(), $model->getPerPage());

            /** @phpstan-ignore-next-line maybe error */
            $scope = (clone $queryBuilder)
                ->select([$keyName])
                ->setEagerLoads([])
                ->forPage($this->page, $perPage)->get();

            /** @phpstan-ignore-next-line maybe error */
            $queryBuilder->whereIn($keyName, $scope->pluck($keyName));
        }
        /** @phpstan-ignore-next-line maybe error */
        return $queryBuilder;
    }

    /**
     * Export data with scope.
     *
     * @param string $scope
     *
     * @return $this
     */
    public function withScope($scope)
    {
        if ($scope == Exporter::SCOPE_ALL) {
            return $this;
        }

        list($scope, $args) = explode(':', $scope);

        if ($scope == Exporter::SCOPE_CURRENT_PAGE) {
            $this->grid->model()->usePaginate(true);
            $this->page = $args ?: 1;
        }

        if ($scope == Exporter::SCOPE_SELECTED_ROWS) {
            $selected = explode(',', $args);
            $this->grid->model()->whereIn($this->grid->getKeyName(), $selected);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function export();
}
