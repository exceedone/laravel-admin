<?php

namespace OpenAdmin\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;


class Table extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.table';

    /**
     * @var array<mixed>
     */
    protected $headers = [];

    /**
     * @var array<mixed>
     */
    protected $rows = [];

    /**
     * @var array<mixed>
     */
    protected $style = [];

    /**
     * @var array<mixed>
     */
    protected $columnStyle = [];

    /**
     * @var array<mixed>
     */
    protected $columnClasses = [];

    /**
     * Table constructor.
     *
     * @param array<mixed> $headers
     * @param array<mixed> $rows
     * @param array<mixed> $style
     */
    public function __construct($headers = [], $rows = [], $style = [])
    {
        $this->setHeaders($headers);
        $this->setRows($rows);
        $this->setStyle($style);

        $this->class('table '.implode(' ', $this->style));
    }

    /**
     * Set table headers.
     *
     * @param array<mixed> $headers
     *
     * @return $this
     */
    public function setHeaders($headers = [])
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Set table rows.
     *
     * @param array<mixed> $rows
     *
     * @return $this
     */
    public function setRows($rows = [])
    {
        if (Arr::isAssoc($rows)) {
            foreach ($rows as $key => $item) {
                $this->rows[] = [$key, $item];
            }

            return $this;
        }

        $this->rows = $rows;

        return $this;
    }

    /**
     * Set table style.
     *
     * @param array<mixed> $style
     *
     * @return $this
     */
    public function setStyle($style = [])
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Set table column style.
     *
     * @param array<mixed> $style
     *
     * @return $this
     */
    public function setColumnStyle($style = [])
    {
        $this->columnStyle = $style;

        return $this;
    }

    /**
     * Set table column classes.
     * @param array<mixed> $classes
     * @return $this
     */
    public function setColumnClasses($classes = [])
    {
        $this->columnClasses = $classes;

        return $this;
    }

    /**
     * Render the table.
     *
     * @return string
     */
    public function render()
    {
        $vars = [
            'headers'      => $this->headers,
            'rows'         => $this->rows,
            'style'        => $this->style,
            'columnStyle'  => $this->columnStyle,
            'columnClasses'  => $this->columnClasses,
            'attributes'   => $this->formatAttributes(),
        ];

        return view($this->view, $vars)->render();
    }
}
