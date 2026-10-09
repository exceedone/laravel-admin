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
    protected function formatBetweenName($column)
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
     * Override parent formatName to use our formatBetweenName method
     * @param string $column
     * @return array<string, string>|string|null
     */
    protected function formatName($column)
    {
        return $this->formatBetweenName($column);
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

        // @phpstan-ignore-next-line Assigned value is always array|string at runtime
        $this->value = Arr::get($inputs, $this->column);

        // @phpstan-ignore-next-line $array is always array at runtime
        $value = array_filter($this->value, function ($val) {
            return $val !== '';
        });

        if (empty($value)) {
            return;
        }

        if (!isset($value['start'])) {
            return $this->buildCondition($this->column, '<=', $value['end']);
        }

        if (!isset($value['end'])) {
            return $this->buildCondition($this->column, '>=', $value['start']);
        }

        $this->query = 'whereBetween';

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

        // Type assertion for PHPStan - maintain original behavior
        /** @var array{start: string, end: string} $idArray */
        $idArray = $this->id;
        $startId = $idArray['start'];
        $endId = $idArray['end'];
        
        $script = <<<EOT
            $('#{$startId}').datetimepicker($startOptions);
            $('#{$endId}').datetimepicker($endOptions);
            $("#{$startId}").on("dp.change", function (e) {
                $('#{$endId}').data("DateTimePicker").minDate(e.date);
            });
            $("#{$endId}").on("dp.change", function (e) {
                $('#{$startId}').data("DateTimePicker").maxDate(e.date);
            });
EOT;

        Admin::script($script);
    }
}
