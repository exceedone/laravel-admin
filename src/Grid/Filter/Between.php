<?php

namespace Encore\Admin\Grid\Filter;

use Encore\Admin\Admin;
use Illuminate\Support\Arr;

class Between extends AbstractFilter
{
    /**
     * {@inheritdoc}
     * @var string
     */
    protected $view = 'admin::filter.between';

    /**
     * Format id.
     *
     * @param string $column
     *
     * @return array<mixed>|string
     */
    public function formatId($column)
    {
        $id = str_replace('.', '_', $column);

        return ['start' => "{$id}_start", 'end' => "{$id}_end"];
    }

    /**
     * Format two field names of this filter.
     * @param string $column
     *
     * @return array<string, string>
     */
    /** @phpstan-ignore-next-line Return type (array<string, string>) of method Encore\Admin\Grid\Filter\Between::formatName() should be compatible with return type (string|null) of method Encore\Admin\Grid\Filter\AbstractFilter::formatName() */
    protected function formatName($column)
    {
        $columns = explode('.', $column);

        if (count($columns) == 1) {
            $name = $columns[0];
        } else {
            $name = array_shift($columns);

            foreach ($columns as $column) {
                $name .= "[$column]";
            }
        }

        return ['start' => "{$name}[start]", 'end' => "{$name}[end]"];
    }

    /**
     * Get condition of this filter.
     *
     * @param array<mixed> $inputs
     *
     * @return mixed
     */
    public function condition($inputs)
    {
        if (!Arr::has($inputs, $this->column)) {
            return;
        }

        $this->value = Arr::get($inputs, $this->column);

        $value = array_filter($this->value, function ($val) {
            return $val !== '';
        });

        if (empty($value)) {
            return;
        }

        if (!isset($value['start'])) {
            /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::buildCondition() should return mixed but returns array<string, array<int, mixed>>|array<string, mixed>. */
            return $this->buildCondition($this->column, '<=', $value['end']);
        }

        if (!isset($value['end'])) {
            /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::buildCondition() should return mixed but returns array<string, array<int, mixed>>|array<string, mixed>. */
            return $this->buildCondition($this->column, '>=', $value['start']);
        }

        $this->query = 'whereBetween';

        /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::buildCondition() should return mixed but returns array<string, array<int, mixed>>|array<string, mixed>. */
        return $this->buildCondition($this->column, $this->value);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    public function datetime($options = [])
    {
        $this->view = 'admin::filter.betweenDatetime';

        $this->setupDatetime($options);

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return void
     */
    protected function setupDatetime($options = [])
    {
        $options['format'] = Arr::get($options, 'format', 'YYYY-MM-DD HH:mm:ss');
        $options['locale'] = Arr::get($options, 'locale', config('app.locale'));

        $startOptions = json_encode($options);
        $endOptions = json_encode($options + ['useCurrent' => false]);

        $script = <<<EOT
            /** @phpstan-ignore-next-line Cannot access offset 'start' on array<mixed>|string. */
            $('#{$this->id['start']}').datetimepicker($startOptions);
            /** @phpstan-ignore-next-line Cannot access offset 'end' on array<mixed>|string. */
            $('#{$this->id['end']}').datetimepicker($endOptions);
            /** @phpstan-ignore-next-line Cannot access offset 'start' on array<mixed>|string. */
            $("#{$this->id['start']}").on("dp.change", function (e) {
                /** @phpstan-ignore-next-line Cannot access offset 'end' on array<mixed>|string. */
                $('#{$this->id['end']}').data("DateTimePicker").minDate(e.date);
            });
            /** @phpstan-ignore-next-line Cannot access offset 'end' on array<mixed>|string. */
            $("#{$this->id['end']}").on("dp.change", function (e) {
                /** @phpstan-ignore-next-line Cannot access offset 'start' on array<mixed>|string. */
                $('#{$this->id['start']}').data("DateTimePicker").maxDate(e.date);
            });
EOT;

        Admin::script($script);
    }
}
