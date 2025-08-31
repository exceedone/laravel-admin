<?php

namespace Encore\Admin\Grid\Filter;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Filter;
use Encore\Admin\Grid\Filter\Presenter\Checkbox;
use Encore\Admin\Grid\Filter\Presenter\DateTime;
use Encore\Admin\Grid\Filter\Presenter\MultipleSelect;
use Encore\Admin\Grid\Filter\Presenter\Presenter;
use Encore\Admin\Grid\Filter\Presenter\Radio;
use Encore\Admin\Grid\Filter\Presenter\Select;
use Encore\Admin\Grid\Filter\Presenter\Text;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class AbstractFilter.
 *
 * @method Text url()
 * @method Text email()
 * @method Text integer()
 * @method Text decimal($options = [])
 * @method Text currency($options = [])
 * @method Text percentage($options = [])
 * @method Text ip()
 * @method Text mac()
 * @method Text mobile($mask = '19999999999')
 * @method Text inputmask($options = [], $icon = '')
 * @method Text placeholder($placeholder = '')
 */
abstract class AbstractFilter
{
    /**
     * Element id.
     *
     * @var array<mixed>|string
     */
    protected $id;

    /**
     * Label of presenter.
     *
     * @var string
     */
    protected $label;

    /**
     * @var array<mixed>|string
     */
    protected $value;

    /**
     * @var bool
     */
    protected $isnull = false;

    /**
     * @var bool
     */
    protected $nullcheck = false;

    /**
     * @var array<mixed>|string
     */
    protected $defaultValue;

    /**
     * @var string
     */
    protected $column;

    /**
     * Presenter object.
     *
     * @var Presenter
     */
    protected $presenter;

    /**
     * Query for filter.
     *
     * @var string
     */
    protected $query = 'where';

    /**
     * @var Filter
     */
    protected $parent;

    /**
     * @var string
     */
    protected $view = 'admin::filter.where';

    /**
     * @var Collection<int|string, mixed>
     */
    public $group;

    /**
     * AbstractFilter constructor.
     *
     * @param mixed $column
     * @param string $label
     */
    public function __construct($column, $label = '')
    {
        $this->column = $column;
        $this->label = $this->formatLabel($label);
        $this->id = $this->formatId($column);

        $this->setupDefaultPresenter();
    }

    /**
     * Setup default presenter.
     *
     * @return void
     */
    protected function setupDefaultPresenter()
    {
        $this->setPresenter(new Text($this->label));
    }

    /**
     * Format label.
     *
     * @param string $label
     *
     * @return string
     */
    protected function formatLabel($label)
    {
        $label = $label ?: ucfirst($this->column);

        return str_replace(['.', '_'], ' ', $label);
    }

    /**
     * Format name.
     *
     * @param string $column
     * 
     * @return string|null
     */
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

        $parenName = $this->parent->getName();

        return $parenName ? "{$parenName}_{$name}" : $name;
    }

    /**
     * Format id.
     *
     * @param array<mixed>|string $columns
     *
     * @return array<mixed>|string
     */
    protected function formatId($columns)
    {
        return str_replace('.', '_', $columns);
    }

    /**
     * @param Filter $filter
     * 
     * @return void
     */
    public function setParent(Filter $filter)
    {
        $this->parent = $filter;
    }

    /**
     * Get siblings of current filter.
     *
     * @param null $index
     *
     * @return AbstractFilter[]|mixed
     */
    public function siblings($index = null)
    {
        if (!is_null($index)) {
            return Arr::get($this->parent->filters(), $index);
        }

        return $this->parent->filters();
    }

    /**
     * Get previous filter.
     *
     * @param int $step
     *
     * @return AbstractFilter[]|mixed
     */
    public function previous($step = 1)
    {
        return $this->siblings(
            array_search($this, $this->parent->filters()) - $step
        );
    }

    /**
     * Get next filter.
     *
     * @param int $step
     *
     * @return AbstractFilter[]|mixed
     */
    public function next($step = 1)
    {
        return $this->siblings(
            array_search($this, $this->parent->filters()) + $step
        );
    }

    /**
     * Get query condition from filter.
     *
     * @param array<mixed> $inputs
     *
     * @return array<mixed>|mixed|null
     */
    public function getCondition($inputs)
    {
        $isnull = Arr::get($inputs, 'isnull-'. $this->column);

        if (isset($isnull)) {
            return $this->whereNullCondition();
        }

        return $this->condition($inputs);
    }

    /**
     * Get query condition from filter.
     *
     * @param array<mixed> $inputs
     *
     * @return array<mixed>|mixed|null
     */
    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if (!isset($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, $this->value);
    }

    /**
     * Get query where null condition from filter.
     *
     * @return array<mixed>|mixed|null
     */
    public function whereNullCondition()
    {
        $this->isnull = true;
        $this->query = 'whereNull';
        return $this->buildCondition($this->column);
    }

    /**
     * Select filter.
     *
     * @param array<mixed>|\Illuminate\Support\Collection<int|string,mixed> $options
     *
     * @return Select
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::select() should return Encore\Admin\Grid\Filter\Presenter\Select but returns Encore\Admin\Grid\Filter\Presenter\Presenter. */
    public function select($options = [])
    {
        /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::setPresenter() should return mixed but returns Encore\Admin\Grid\Filter\Presenter\Presenter. */
        return $this->setPresenter(new Select($options));
    }

    /**
     * @param array<mixed>|\Illuminate\Support\Collection<int|string, mixed> $options
     *
     * @return MultipleSelect
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::multipleSelect() should return Encore\Admin\Grid\Filter\Presenter\MultipleSelect but returns Encore\Admin\Grid\Filter\Presenter\Presenter. */
    public function multipleSelect($options = [])
    {
        /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::setPresenter() should return mixed but returns Encore\Admin\Grid\Filter\Presenter\Presenter. */
        return $this->setPresenter(new MultipleSelect($options));
    }

    /**
     * @param array<mixed>|\Illuminate\Support\Collection<int|string, mixed> $options
     *
     * @return Radio
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::radio() should return Encore\Admin\Grid\Filter\Presenter\Radio but returns Encore\Admin\Grid\Filter\Presenter\Presenter. */
    public function radio($options = [])
    {
        /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::setPresenter() should return mixed but returns Encore\Admin\Grid\Filter\Presenter\Presenter. */
        return $this->setPresenter(new Radio($options));
    }

    /**
     * @param array<mixed>|\Illuminate\Support\Collection<int|string, mixed> $options
     *
     * @return Checkbox
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::checkbox() should return Encore\Admin\Grid\Filter\Presenter\Checkbox but returns Encore\Admin\Grid\Filter\Presenter\Presenter. */
    public function checkbox($options = [])
    {
        /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::setPresenter() should return mixed but returns Encore\Admin\Grid\Filter\Presenter\Presenter. */
        return $this->setPresenter(new Checkbox($options));
    }

    /**
     * Datetime filter.
     *
     * @param array<mixed>|\Illuminate\Support\Collection<int|string, mixed> $options
     *
     * @return mixed
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::datetime() should return mixed but returns Encore\Admin\Grid\Filter\Presenter\Presenter. */
    public function datetime($options = [])
    {
        /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::setPresenter() should return mixed but returns Encore\Admin\Grid\Filter\Presenter\Presenter. */
        return $this->setPresenter(new DateTime($options));
    }

    /**
     * Date filter.
     *
     * @return DateTime
     */
    public function date()
    {
        return $this->datetime(['format' => 'YYYY-MM-DD']);
    }

    /**
     * Time filter.
     *
     * @return DateTime
     */
    public function time()
    {
        return $this->datetime(['format' => 'HH:mm:ss']);
    }

    /**
     * Day filter.
     *
     * @return DateTime
     */
    public function day()
    {
        return $this->datetime(['format' => 'DD']);
    }

    /**
     * Month filter.
     *
     * @return DateTime
     */
    public function month()
    {
        return $this->datetime(['format' => 'MM']);
    }

    /**
     * Year filter.
     *
     * @return DateTime
     */
    public function year()
    {
        return $this->datetime(['format' => 'YYYY']);
    }

    /**
     * show isnull condition.
     *
     * @return $this
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::showNullCheck() should return $this(Encore\Admin\Grid\Filter\AbstractFilter) but returns Encore\Admin\Grid\Filter\AbstractFilter. */
    public function showNullCheck()
    {
        $this->nullcheck = true;
        return $this;
    }

    /**
     * Set presenter object of filter.
     *
     * @param Presenter $presenter
     *
     * @return mixed
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::setPresenter() should return mixed but returns Encore\Admin\Grid\Filter\Presenter\Presenter. */
    protected function setPresenter(Presenter $presenter)
    {
        $presenter->setParent($this);

        /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\Presenter\Presenter::setParent() has no return type specified. */
        $presenter->setParent($this);

        return $this->presenter = $presenter;
    }

    /**
     * Get presenter object of filter.
     *
     * @return Presenter
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::presenter() should return Encore\Admin\Grid\Filter\Presenter\Presenter but returns Encore\Admin\Grid\Filter\Presenter\Presenter|null. */
    protected function presenter()
    {
        return $this->presenter;
    }

    /**
     * Set default value for filter.
     *
     * @param array<mixed>|string|null $default
     *
     * @return $this
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::default() should return $this(Encore\Admin\Grid\Filter\AbstractFilter) but returns Encore\Admin\Grid\Filter\AbstractFilter. */
    public function default($default = null)
    {
        /** @phpstan-ignore-next-line If condition is always true. */
        if ($default) {
            $this->defaultValue = $default;
        }

        return $this;
    }

    /**
     * Get element id.
     *
     * @return array<mixed>|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set element id.
     *
     * @param string $id
     *
     * @return $this
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::setId() should return $this(Encore\Admin\Grid\Filter\AbstractFilter) but returns Encore\Admin\Grid\Filter\AbstractFilter. */
    public function setId($id)
    {
        $this->id = $this->formatId($id);

        return $this;
    }

    /**
     * Get column name of current filter.
     *
     * @return string
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::getColumn() should return string but returns string|null. */
    public function getColumn()
    {
        /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter::getName() has no return type specified. */
        $parentName = $this->parent->getName();

        return $parentName ? "{$parentName}_{$this->column}" : $this->column;
    }

    /**
     * Get value of current filter.
     *
     * @return array<mixed>|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Build conditions of filter.
     *
     * @return mixed
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::buildCondition() should return mixed but returns array<string, array<int, mixed>>|array<string, mixed>. */
    protected function buildCondition()
    {
        $column = explode('.', $this->column);

        if (count($column) == 1) {
            /** @phpstan-ignore-next-line Function func_get_args() should be used only within a function with variadic arguments. */
            return [$this->query => func_get_args()];
        }

        /** @phpstan-ignore-next-line Function func_get_args() should be used only within a function with variadic arguments. */
        return $this->buildRelationQuery(...func_get_args());
    }

    /**
     * Build query condition of model relation.
     *
     * @return array<string, mixed>
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::buildRelationQuery() should return array<string, mixed> but returns array<string, array<int, \Closure>|string>. */
    protected function buildRelationQuery()
    {
        /** @phpstan-ignore-next-line Function func_get_args() should be used only within a function with variadic arguments. */
        $args = func_get_args();

        list($relation, $args[0]) = explode('.', $this->column);

        return ['whereHas' => [$relation, function ($relation) use ($args) {
            /** @phpstan-ignore-next-line Function call_user_func_array() with array{mixed, string} will throw a TypeError. */
            call_user_func_array([$relation, $this->query], $args);
        }]];
    }

    /**
     * Variables for filter view.
     *
     * @return array<string, mixed>
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::variables() should return array<string, mixed> but returns array<array<string, mixed>|bool|string, mixed>. */
    protected function variables()
    {
        return array_merge([
            'id'        => $this->id,
            /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::formatName() should return string|null but returns string|array<string, string>. */
            'name'      => $this->formatName($this->column),
            'column'    => $this->column,
            'label'     => $this->label,
            'value'     => $this->value ?: $this->defaultValue,
            'nullcheck' => $this->nullcheck,
            'isnull'    => $this->isnull? 'checked': '',
            'presenter' => $this->presenter(),
        /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\Presenter\Presenter::variables() has no return type specified. */
        ], $this->presenter()->variables());
    }

    /**
     * Render this filter.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $script = "$('.isnull-{$this->column}').iCheck({checkboxClass:'icheckbox_minimal-blue'});";
        /** @phpstan-ignore-next-line Method Encore\Admin\Facades\Admin::script() has no return type specified. */
        Admin::script($script);

        return view($this->view, $this->variables());
    }

    /**
     * Render this filter.
     *
     * @return \Illuminate\View\View|string
     */
    /** @phpstan-ignore-next-line Method Encore\Admin\Grid\Filter\AbstractFilter::__toString() should return string but returns \Illuminate\View\View|string. */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param string $method
     * @param array<mixed> $params
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        if (method_exists($this->presenter, $method)) {
            /** @phpstan-ignore-next-line Call to method {method}() on an unknown class Encore\Admin\Grid\Filter\Presenter\Presenter. */
            return $this->presenter()->{$method}(...$params);
        }

        throw new \Exception('Method "'.$method.'" not exists.');
    }
}
