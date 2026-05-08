<?php

namespace Encore\Admin\Console;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ResourceGenerator
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array<string, string>
     */
    protected $formats = [
        'form_field'  => "\$form->%s('%s', __('%s'))",
        'show_field'  => "\$show->field('%s', __('%s'))",
        'grid_column' => "\$grid->column('%s', __('%s'))",
    ];

    /**
     * @var array<string, string>
     */
    protected $fieldTypeMapping = [
        'ip'       => 'ip',
        'email'    => 'email|mail',
        'password' => 'password|pwd',
        'url'      => 'url|link|src|href',
        'mobile'   => 'mobile|phone',
        'color'    => 'color|rgb',
        'image'    => 'image|img|avatar|pic|picture|cover',
        'file'     => 'file|attachment',
    ];

    /**
     * ResourceGenerator constructor.
     *
     * @param mixed $model
     */
    public function __construct($model)
    {
        $this->model = $this->getModel($model);
    }

    /**
     * @param mixed $model
     *
     * @return mixed
     */
    protected function getModel($model)
    {
        if ($model instanceof Model) {
            return $model;
        }

        if (!class_exists($model) || !is_string($model) || !is_subclass_of($model, Model::class)) {
            throw new \InvalidArgumentException("Invalid model [$model] !");
        }

        return new $model();
    }

    /**
     * @return string
     */
    public function generateForm()
    {
        $reservedColumns = $this->getReservedColumns();

        $output = '';

        foreach ($this->getTableColumns() as $column) {
            $name = $column->getName();
            if (in_array($name, $reservedColumns)) {
                continue;
            }
            $type = $column->getType()->getName();
            $default = $column->getDefault();

            $defaultValue = '';

            // set column fieldType and defaultValue
            switch ($type) {
                case 'boolean':
                case 'bool':
                    $fieldType = 'switch';
                    break;
                case 'json':
                case 'array':
                case 'object':
                    $fieldType = 'text';
                    break;
                case 'string':
                    $fieldType = 'text';
                    foreach ($this->fieldTypeMapping as $type => $regex) {
                        if (preg_match("/^($regex)$/i", $name) !== 0) {
                            $fieldType = $type;
                            break;
                        }
                    }
                    $defaultValue = "'{$default}'";
                    break;
                case 'integer':
                case 'bigint':
                case 'smallint':
                case 'timestamp':
                    $fieldType = 'number';
                    break;
                case 'decimal':
                case 'float':
                case 'real':
                    $fieldType = 'decimal';
                    break;
                case 'datetime':
                    $fieldType = 'datetime';
                    $defaultValue = "date('Y-m-d H:i:s')";
                    break;
                case 'date':
                    $fieldType = 'date';
                    $defaultValue = "date('Y-m-d')";
                    break;
                case 'time':
                    $fieldType = 'time';
                    $defaultValue = "date('H:i:s')";
                    break;
                case 'text':
                case 'blob':
                    $fieldType = 'textarea';
                    break;
                default:
                    $fieldType = 'text';
                    $defaultValue = "'{$default}'";
            }

            $defaultValue = $defaultValue ?: $default;

            $label = $this->formatLabel($name);

            $output .= sprintf($this->formats['form_field'], $fieldType, $name, $label);

            if (trim($defaultValue, "'\"")) {
                $output .= "->default({$defaultValue})";
            }

            $output .= ";\r\n";
        }

        return $output;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function generateShow()
    {
        $output = '';

        foreach ($this->getTableColumns() as $column) {
            $name = $column['name'];

            // set column label
            $label = $this->formatLabel($name);

            $output .= sprintf($this->formats['show_field'], $name, $label);

            $output .= ";\r\n";
        }

        return $output;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function generateGrid()
    {
        $output = '';

        foreach ($this->getTableColumns() as $column) {
            $name  = $column['name'];
            $label = $this->formatLabel($name);

            $output .= sprintf($this->formats['grid_column'], $name, $label);
            $output .= ";\r\n";
        }

        return $output;
    }

    /**
     * @return array<string>
     */
    protected function getReservedColumns()
    {
        return [
            $this->model->getKeyName(),
            $this->model->getCreatedAtColumn(),
            $this->model->getUpdatedAtColumn(),
            'deleted_at',
        ];
    }

    /**
     * Get columns of the model's table using Laravel's native schema builder.
     *
     * Each element is an associative array with keys:
     *   name, type_name, type, collation, nullable, default, auto_increment, comment
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getTableColumns(): array
    {
        $connection = $this->model->getConnection();
        $table      = $connection->getTablePrefix().$this->model->getTable();

        // Strip database prefix if table contains a dot (e.g. "database.table")
        if (str_contains($table, '.')) {
            [, $table] = explode('.', $table, 2);
        }

        return Schema::connection($connection->getName())->getColumns($table);
    }

    /**
     * Format label.
     *
     * @param string $value
     *
     * @return string
     */
    protected function formatLabel($value)
    {
        return ucfirst(str_replace(['-', '_'], ' ', $value));
    }
}
