<?php

namespace Encore\Admin\Widgets\Grid;

use Encore\Admin\Widgets\Grid\Exporters\AbstractExporter;
use Encore\Admin\Widgets\Grid\Grid;
use Encore\Admin\Widgets\Grid\Exporters\CsvExporter;

class Exporter
{
    /**
     * Export scope constants.
     */
    const SCOPE_ALL = 'all';
    const SCOPE_CURRENT_PAGE = 'page';
    const SCOPE_SELECTED_ROWS = 'selected';

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Available exporter drivers.
     *
     * @var array<mixed>
     */
    protected static $drivers = [];

    /**
     * Export query name.
     *
     * @var string
     */
    public static $queryName = '_export_';

    /**
     * Create a new Exporter instance.
     *
     * @param Grid $grid
     *
     * @return void
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Set export query name.
     *
     * @param string $name
     *
     * @return void
     */
    public static function setQueryName($name)
    {
        static::$queryName = $name;
    }

    /**
     * Extends new exporter driver.
     *
     * @param mixed $driver
     * @param mixed $extend
     *
     * @return void
     */
    public static function extend($driver, $extend)
    {
        static::$drivers[$driver] = $extend;
    }

    /**
     * Resolve export driver.
     *
     * @param Exporters\AbstractExporter|string $driver
     *
     * @return AbstractExporter
     */
    public function resolve($driver)
    {
        if ($driver instanceof Exporters\AbstractExporter) {
            return $driver->setGrid($this->grid);
        }

        return $this->getExporter($driver);
    }

    /**
     * Get export driver.
     *
     * @param string $driver
     *
     * @return CsvExporter
     */
    protected function getExporter($driver)
    {
        if (!array_key_exists($driver, static::$drivers)) {
            return $this->getDefaultExporter();
        }

        /** @phpstan-ignore-next-line Method Encore\Admin\Widgets\Grid\Exporter::getExporter() should return Encore\Admin\Widgets\Grid\Exporters\CsvExporter but returns object. */
        return new static::$drivers[$driver]($this->grid);
    }

    /**
     * Get default exporter.
     *
     * @return CsvExporter
     */
    public function getDefaultExporter()
    {
        return new CsvExporter($this->grid);
    }

    /**
     * Format query for export url.
     *
     * @param string|int $scope
     * @param string|null $args
     * @return string[]
     */
    public static function formatExportQuery($scope = '', $args = null)
    {
        $query = '';

        if ($scope == static::SCOPE_ALL) {
            $query = 'all';
        }

        if ($scope == static::SCOPE_CURRENT_PAGE) {
            $query = "page:$args";
        }

        if ($scope == static::SCOPE_SELECTED_ROWS) {
            $query = "selected:$args";
        }

        return [static::$queryName => $query];
    }
}
