<?php

namespace OpenAdmin\Admin\Form\Field;

use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\Form\Field;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Arr;

class KeyValue extends Field
{
    /**
     * @var array<string, string>
     */
    protected $value = ['' => ''];

    /**
     * Fill data to the field.
     *
     * @param array<mixed> $data
     *
     * @return void
     */
    public function fill($data)
    {
        $this->data = $data;

        $this->value = Arr::get($data, $this->column, $this->value);

        $this->formatValue();
    }

    /**
     * {@inheritdoc}
     * @param array<mixed> $input
     * @return bool|Validator|Factory
     */
    public function getValidator(array $input)
    {
        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        if (!is_string($this->column)) {
            return false;
        }

        $rules = $attributes = [];

        if (!$fieldRules = $this->getRules()) {
            return false;
        }

        if (!Arr::has($input, $this->column)) {
            return false;
        }

        $rules["{$this->column}.keys.*"] = 'distinct';
        $rules["{$this->column}.values.*"] = $fieldRules;
        $attributes["{$this->column}.keys.*"] = __('Key');
        $attributes["{$this->column}.values.*"] = __('Value');

        return validator($input, $rules, $this->getValidationMessages(), $attributes);
    }

    /**
     * @return void
     */
    protected function setupScript()
    {
        $this->script = <<<SCRIPT

$('.{$this->column}-add').on('click', function () {
    var tpl = $('template.{$this->column}-tpl').html();
    $('tbody.kv-{$this->column}-table').append(tpl);
});

$('tbody').on('click', '.{$this->column}-remove', function () {
    $(this).closest('tr').remove();
});

SCRIPT;
    }

    /**
     * @param array<mixed> $value
     * @return array<mixed>
     */
    public function prepare($value)
    {
        return array_combine($value['keys'], $value['values']);
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->setupScript();

        Admin::style('td .form-group {margin-bottom: 0 !important;}');

        return parent::render();
    }
}
