<?php

namespace Encore\Admin\Form;

use Encore\Admin\Form;
use Illuminate\Support\Collection;

class Tab
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Collection<int|string, mixed>
     */
    protected $tabs;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * Tab constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;

        $this->tabs = new Collection();
    }

    /**
     * Append a tab section.
     *
     * @param string   $title
     * @param \Closure $content
     * @param bool     $active
     *
     * @return $this
     */
    public function append($title, \Closure $content, $active = false)
    {
        $fields = $this->collectFields($content);

        $id = 'form-'.($this->tabs->count() + 1);

        $this->tabs->push(compact('id', 'title', 'fields', 'active'));

        return $this;
    }

    /**
     * Collect fields under current tab.
     *
     * @param \Closure $content
     *
     * @return Collection<int|string, Field>
     */
    protected function collectFields(\Closure $content)
    {
        call_user_func($content, $this->form);

        $fields = clone $this->form->builder()->fields();

        $all = $fields->toArray();

        foreach ($this->form->rows as $row) {
            $rowFields = array_map(function ($field) {
                return $field['element'];
            // @phpstan-ignore-next-line The value is always an object exposing getFields() at runtime
            }, $row->getFields());

            $match = false;

            foreach ($rowFields as $field) {
                if (($index = array_search($field, $all)) !== false) {
                    if (!$match) {
                        // @phpstan-ignore-next-line $value is always Encore\Admin\Form\Field at runtime
                        $fields->put($index, $row);
                    } else {
                        $fields->pull($index);
                    }

                    $match = true;
                }
            }
        }

        $fields = $fields->slice($this->offset);

        $this->offset += $fields->count();

        return $fields;
    }

    /**
     * Get all tabs.
     *
     * @return Collection<int|string, mixed>
     */
    public function getTabs()
    {
        // If there is no active tab, then active the first.
        // @phpstan-ignore-next-line The callback matches the expected signature at runtime
        if ($this->tabs->filter(function ($tab) {
            // @phpstan-ignore-next-line The value is always an array at runtime
            return $tab['active'];
        })->isEmpty()) {
            $first = $this->tabs->first();
            // @phpstan-ignore-next-line The value is always an array at runtime
            $first['active'] = true;

            $this->tabs->offsetSet(0, $first);
        }

        return $this->tabs;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->tabs->isEmpty();
    }
}
