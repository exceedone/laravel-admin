<?php

namespace OpenAdmin\Admin\Form\Field;

use OpenAdmin\Admin\Form\NestedForm;

class Table extends HasMany
{
    /**
     * @var string
     */
    protected $viewMode = 'table';

    /**
     * Table constructor.
     *
     * @param string $column
     * @param array<int, mixed>  $arguments
     */
    public function __construct($column, $arguments = [])
    {
        $this->column = $column;

        if (count($arguments) == 1) {
            $this->label = $this->formatLabel();
            $this->builder = $arguments[0];
        }

        if (count($arguments) == 2) {
            list($this->label, $this->builder) = $arguments;
        }
    }

    /**
     * @return array<mixed>
     */
    protected function buildRelatedForms()
    {
        if (is_null($this->form)) {
            return [];
        }

        $forms = [];

        if ($values = old($this->column)) {
            foreach ($values as $key => $data) {
                if ($data[NestedForm::REMOVE_FLAG_NAME] == 1) {
                    continue;
                }
                /** @phpstan-ignore-next-line fill method require 2 parameters. */
                $forms[$key] = $this->buildNestedForm($this->column, $this->builder, $key)->fill($data);
            }
        } else {
            foreach ($this->value as $key => $data) {
                if (isset($data['pivot'])) {
                    $data = array_merge($data, $data['pivot']);
                }
                /** @phpstan-ignore-next-line fill method require 2 parameters. */
                $forms[$key] = $this->buildNestedForm($this->column, $this->builder, $key)->fill($data);
            }
        }

        return $forms;
    }

    /**
     * @param mixed $input
     * @return array<mixed>
     */
    public function prepare($input)
    {
        $form = $this->buildNestedForm($this->column, $this->builder);

        $prepare = $form->prepare($input);
        /** @phpstan-ignore-next-line Unable to resolve the template type TKey in call to function collect  */
        return collect($prepare)->reject(function ($item) {
            return $item[NestedForm::REMOVE_FLAG_NAME] == 1;
        })->map(function ($item) {
            unset($item[NestedForm::REMOVE_FLAG_NAME]);

            return $item;
        })->toArray();
    }

    /**
     * @return string|void
     */
    protected function getKeyName()
    {
        if (is_null($this->form)) {
            return;
        }

        return 'id';
    }

    /**
     * @param string $column
     * @param \Closure $builder
     * @param mixed $key
     * @param mixed $index
     * @return NestedForm
     */
    protected function buildNestedForm($column, \Closure $builder, $key = null, $index = null)
    {
        $form = new NestedForm($column);

        $form->setIndex($index);

        $form->setForm($this->form)
            ->setKey($key);

        call_user_func($builder, $form);

        $form->hidden(NestedForm::REMOVE_FLAG_NAME)->default(0)->addElementClass(NestedForm::REMOVE_FLAG_CLASS);

        return $form;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|string
     * @throws \Exception
     */
    public function render()
    {
        return $this->renderTable();
    }
}
