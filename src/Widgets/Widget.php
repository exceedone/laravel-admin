<?php

namespace OpenAdmin\Admin\Widgets;

use Illuminate\Support\Fluent;

/**
 * @method $this class(array|string $class)
 * @method $this id(mixed $id)
 * @method $this style(string $styles)
 * @method $this addRelationColumn(string $name, string $label)
 * @method $this addJsonColumn(string $name, string $label)
 * @property mixed $id
 * @property mixed $width
 * @property mixed $height
 * @property mixed $variables
 * @property mixed $originalCollection
 * @property mixed $paginator
 * @method width(mixed $width)
 * @method height(mixed $height)
 *
 * @extends Fluent <int|string, mixed>
 */
abstract class Widget extends Fluent
{
    /**
     * @var string
     */
    protected $view;

    /**
     * @return mixed
     */
    abstract public function render();

    /**
     * Set view of widget.
     *
     * @param string $view
     *
     * @return void
     */
    public function view($view)
    {
        $this->view = $view;
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @return string
     */
    public function formatAttributes()
    {
        $html = [];
        foreach ((array) $this->getAttributes() as $key => $value) {
            $element = $this->attributeElement($key, $value);
            if (!is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param string $key
     * @param string|null $value
     *
     * @return string|void
     */
    protected function attributeElement($key, $value)
    {
        if (is_numeric($key)) {
            $key = $value;
        }
        if (!is_null($value)) {
            return $key.'="'.htmlentities($value, ENT_QUOTES, 'UTF-8').'"';
        }
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->render();
    }
}
