<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;

class DateRange extends Field
{
    /**
     * @var array<string>
     */
    protected static $css = [
        '/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
    ];

    /**
     * @var array<string>
     */
    protected static $js = [
        '/vendor/laravel-admin/moment/min/moment-with-locales.min.js',
        '/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
    ];

    /**
     * @var string
     */
    protected $format = 'YYYY-MM-DD';

    /**
     * Column name.
     *
     * @var array<mixed>
     */
    protected $column = [];

    /**
     * @param mixed $column
     * @param array<int, mixed> $arguments
     */
    public function __construct($column, $arguments)
    {
        $this->column['start'] = $column;
        $this->column['end'] = $arguments[0];

        array_shift($arguments);
        $this->label = $this->formatLabel($arguments);
        $this->id = $this->formatId($this->column);

        $this->options(['format' => $this->format]);
    }

    /**
     * {@inheritdoc}
     * @param mixed $value
     * @return $this
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            if (!isset($this->value['start']) && !isset($this->value['end'])) {
                return $this->getDefault();
            }

            return $this->value;
        }

        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @param mixed $value
     * @return mixed
     */
    public function prepare($value)
    {
        if ($value === '') {
            $value = null;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function render()
    {
        /** @phpstan-ignore-next-line Cannot access offset 'locale' on array<string, mixed>|Closure. */
        $this->options['locale'] = config('app.locale');

        $startOptions = json_encode($this->options);
        $endOptions = json_encode($this->options + ['useCurrent' => false]);

        $class = $this->getElementClassSelector();
        
        // Type assertion for PHPStan - maintain original behavior
        /** @var array{start: string, end: string} $class */
        $startClass = $class['start'];
        $endClass = $class['end'];

        $this->script = <<<EOT
            $('{$startClass}').datetimepicker($startOptions);
            $('{$endClass}').datetimepicker($endOptions);
            $("{$startClass}").on("dp.change", function (e) {
                $('{$endClass}').data("DateTimePicker").minDate(e.date);
            });
            $("{$endClass}").on("dp.change", function (e) {
                $('{$startClass}').data("DateTimePicker").maxDate(e.date);
            });
EOT;

        return parent::render();
    }
}
