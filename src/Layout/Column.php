<?php

namespace OpenAdmin\Admin\Layout;

use OpenAdmin\Admin\Grid;
use Illuminate\Contracts\Support\Renderable;
use OpenAdmin\Admin\Traits\GridWidth;

class Column implements Buildable
{
    use GridWidth;
    
    /**
     * grid system prefix width.
     *
     * @var array<string, int>|int
     */
    protected $width = [];

    /**
     * @var array<mixed>
     */
    protected $contents = [];

    /**
     * Column constructor.
     *
     * @param mixed $content
     * @param int $width
     */
    public function __construct($content, $width = 12)
    {
        if ($content instanceof \Closure) {
            call_user_func($content, $this);
        } else {
            $this->append($content);
        }

        $this->setWidth($width);
    }

    /**
     * Append content to column.
     *
     * @param mixed $content
     *
     * @return $this
     */
    public function append($content)
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Add a row for column.
     *
     * @param mixed $content
     *
     * @return Column
     */
    public function row($content)
    {
        if (!$content instanceof \Closure) {
            $row = new Row($content);
        } else {
            $row = new Row();

            call_user_func($content, $row);
        }

        ob_start();

        $row->build();

        $contents = ob_get_contents();

        ob_end_clean();

        return $this->append($contents);
    }

    /**
     * Build column html.
     *
     * @return void
     */
    public function build()
    {
        $this->startColumn();

        foreach ($this->contents as $content) {
            if ($content instanceof Renderable || $content instanceof Grid) {
                echo $content->render();
            } else {
                echo (string) $content;
            }
        }

        $this->endColumn();
    }

    /**
     * Start column.
     *
     * @return void
     */
    protected function startColumn()
    {
        // get class name using width array
        $classnName = $this->getGridWidthClass();

        echo "<div class=\"{$classnName}\">";
    }

    /**
     * End column.
     *
     * @return void
     */
    protected function endColumn()
    {
        echo '</div>';
    }
}
