<?php

namespace Encore\Admin\Layout;

use Illuminate\Contracts\Support\Renderable;

class Row implements Buildable, Renderable
{
    /**
     * @var Column[]
     */
    protected $columns = [];

    /**
     * row classes.
     *
     * @var array<string>
     */
    protected $class = [];

    /**
     * Element attributes.
     *
     * @var array<mixed>
     */
    protected $attributes = [];

    /**
     * Row constructor.
     *
     * @param string $content
     */
    public function __construct($content = '')
    {
        if (!empty($content)) {
            $this->column(12, $content);
        }
    }

    /**
     * Add a column.
     *
     * @param int $width
     * @param mixed $content
     *
     * @return void
     */
    public function column($width, $content)
    {
        $width = $width < 1 ? round(12 * $width) : $width;

        /** @phpstan-ignore-next-line Parameter #2 $width of class Encore\Admin\Layout\Column constructor expects int, float|int<1, max> given. */
        $column = new Column($content, $width);

        $this->addColumn($column);
    }

    /**
     * Add class in row.
     *
     * @param array<string>|string $class
     *
     * @return $this
     */
    public function class($class)
    {
        if (is_string($class)) {
            $class = [$class];
        }

        $this->class = $class;

        return $this;
    }

    /**
     * Add html attributes to elements.
     *
     * @param array<mixed>|string $attribute
     * @param mixed        $value
     *
     * @return $this
     */
    public function attribute($attribute, $value = null)
    {
        if (is_array($attribute)) {
            $this->attributes = array_merge($this->attributes, $attribute);
        } else {
            // @phpstan-ignore-next-line The value is always castable to string at runtime
            $this->attributes[$attribute] = (string) $value;
        }

        return $this;
    }

    /**
     * Get field attributes.
     *
     * @return array<mixed>
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param Column $column
     *
     * @return void
     */
    protected function addColumn(Column $column)
    {
        $this->columns[] = $column;
    }

    /**
     * Build row column.
     *
     * @return void
     */
    public function build()
    {
        $this->startRow();

        foreach ($this->columns as $column) {
            $column->build();
        }

        $this->endRow();
    }

    /**
     * Start row.
     *
     * @return void
     */
    protected function startRow()
    {
        $class = $this->class;
        $class[] = 'row';
        echo '<div class="'.implode(' ', $class).'" ' . $this->formatAttributes() . '>';
    }

    /**
     * Format the field attributes.
     *
     * @return string
     */
    protected function formatAttributes()
    {
        $html = [];

        foreach ($this->attributes as $name => $value) {
            // @phpstan-ignore-next-line $value is always BackedEnum|Illuminate\Contracts\Support\DeferringDisplayableValue|Illuminate\Contracts\Support\Htmlable|string|null at runtime
            $html[] = $name.'="'.e($value).'"';
        }

        return implode(' ', $html);
    }

    /**
     * End column.
     *
     * @return void
     */
    protected function endRow()
    {
        echo '</div>';
    }

    /**
     * Render row.
     *
     * @return string
     */
    public function render()
    {
        ob_start();

        $this->build();

        $contents = ob_get_contents();

        ob_end_clean();

        /** @phpstan-ignore-next-line Method Encore\Admin\Layout\Row::render() should return string but returns string|false. */
        return $contents;
    }
}
