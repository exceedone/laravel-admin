<?php

namespace Encore\Admin\Grid\Filter;

use Encore\Admin\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Group extends AbstractFilter
{
    /**
     * @var callable|\Closure|string
     */
    protected $builder;

    /**
     * @var string
     */
    protected $name;

    /**
     * Input value from presenter.
     *
     * @var mixed
     */
    public $input;

    /**
     * Group constructor.
     *
     * @param string $column
     * @param string $label
     * @param \Closure|null $builder
     */
    public function __construct($column, $label = '', \Closure $builder = null)
    {
        $this->column = $column;

        if (is_callable($label) && is_null($builder)) {
            $this->builder = $label;
            $this->label = ucfirst($this->column);
        } elseif (is_string($label) && is_callable($builder)) {
            $this->label = $label;
            $this->builder = $builder;
        }

        $this->initialize();
    }

    /**
     * Initialize a group filter.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->id = $this->formatId($this->column);
        $this->group = new Collection();
        /** @phpstan-ignore-next-line Part $this->id (array|string) of encapsed string cannot be cast to string. */
        $this->name = "{$this->id}-filter-group";

        $this->setupDefaultPresenter();
    }

    /**
     * Join a query to group.
     *
     * @param string $label
     * @param array<mixed>  $condition
     *
     * @return $this
     */
    protected function joinGroup($label, array $condition)
    {
        $this->group->push(
            compact('label', 'condition')
        );

        return $this;
    }

    /**
     * Filter out `equal` records.
     *
     * @param string $label
     * @param string $operator
     *
     * @return Group
     */
    public function equal($label = '', $operator = '=')
    {
        $label = $label ?: $operator;

        $condition = [$this->column, $operator, $this->value];

        return $this->joinGroup($label, $condition);
    }

    /**
     * Filter out `not equal` records.
     *
     * @param string $label
     *
     * @return Group
     */
    public function notEqual($label = '')
    {
        return $this->equal($label, '!=');
    }

    /**
     * Filter out `greater then` records.
     *
     * @param string $label
     *
     * @return Group
     */
    public function gt($label = '')
    {
        return $this->equal($label, '>');
    }

    /**
     * Filter out `less then` records.
     *
     * @param string $label
     *
     * @return Group
     */
    public function lt($label = '')
    {
        return $this->equal($label, '<');
    }

    /**
     * Filter out `not less then` records.
     *
     * @param string $label
     *
     * @return Group
     */
    public function nlt($label = '')
    {
        return $this->equal($label, '>=');
    }

    /**
     * Filter out `not greater than` records.
     *
     * @param string $label
     *
     * @return Group
     */
    public function ngt($label = '')
    {
        return $this->equal($label, '<=');
    }

    /**
     * Filter out records that match the regex.
     *
     * @param string $label
     *
     * @return Group
     */
    public function match($label = '')
    {
        $label = $label ?: 'Match';

        return $this->equal($label, 'REGEXP');
    }

    /**
     * Specify a where query.
     *
     * @param string   $label
     * @param \Closure $builder
     *
     * @return Group
     */
    public function where($label, \Closure $builder)
    {
        $this->input = $this->value;

        $condition = [$builder->bindTo($this)];

        return $this->joinGroup($label, $condition);
    }

    /**
     * Specify a where like query.
     *
     * @param string $label
     * @param string $operator
     *
     * @return Group
     */
    public function like($label = '', $operator = 'like')
    {
        $label = $label ?: $operator;

        /** @phpstan-ignore-next-line Part $this->value (array|string) of encapsed string cannot be cast to string. */
        $condition = [$this->column, $operator, "%{$this->value}%"];

        return $this->joinGroup($label, $condition);
    }

    /**
     * Alias of `like` method.
     *
     * @param string $label
     *
     * @return Group
     */
    public function contains($label = '')
    {
        return $this->like($label);
    }

    /**
     * Specify a where ilike query.
     *
     * @param string $label
     *
     * @return Group
     */
    public function ilike($label = '')
    {
        return $this->like($label, 'ilike');
    }

    /**
     * Filter out records which starts with input query.
     *
     * @param string $label
     *
     * @return Group
     */
    public function startWith($label = '')
    {
        $label = $label ?: 'Start with';

        /** @phpstan-ignore-next-line Part $this->value (array|string) of encapsed string cannot be cast to string. */
        $condition = [$this->column, 'like', "{$this->value}%"];

        return $this->joinGroup($label, $condition);
    }

    /**
     * Filter out records which ends with input query.
     *
     * @param string $label
     *
     * @return Group
     */
    public function endWith($label = '')
    {
        $label = $label ?: 'End with';

        /** @phpstan-ignore-next-line Part $this->value (array|string) of encapsed string cannot be cast to string. */
        $condition = [$this->column, 'like', "%{$this->value}"];

        return $this->joinGroup($label, $condition);
    }

    /**
     * {@inheritdoc}
     * @param array<mixed> $inputs
     *
     * @return mixed
     */
    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if (!isset($value)) {
            return;
        }

        // @phpstan-ignore-next-line Assigned value is always array|string at runtime
        $this->value = $value;

        /** @phpstan-ignore-next-line Part $this->id (array|string) of encapsed string cannot be cast to string. */
        $group = Arr::get($inputs, "{$this->id}_group");

        /** @phpstan-ignore-next-line Parameter #1 $callback of function call_user_func expects callable(): mixed, (callable(): mixed)|string given. */
        call_user_func($this->builder, $this);

        // @phpstan-ignore-next-line $key is always int|string at runtime
        if ($query = $this->group->get($group)) {
            // @phpstan-ignore-next-line The value is always an array at runtime (and 1 more mixed-type assumption on this line)
            return $this->buildCondition(...$query['condition']);
        }
    }

    /**
     * Inject script to current page.
     *
     * @return void
     */
    protected function injectScript()
    {
        $script = <<<SCRIPT
$(".{$this->name} li a").click(function(){
    $(".{$this->name}-label").text($(this).text());
    $(".{$this->name}-operation").val($(this).data('index'));
});
SCRIPT;

        Admin::script($script);
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    public function variables()
    {
        // Ensure id is string for request key
        $idString = is_array($this->id) ? implode('_', $this->id) : (string) $this->id;
        $select = request("{$idString}_group");

        // @phpstan-ignore-next-line $key is always int|string at runtime
        $default = $this->group->get($select) ?: $this->group->first();

        return array_merge(parent::variables(), [
            'group_name' => $this->name,
            'default'    => $default,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->injectScript();

        if ($this->builder && $this->group->isEmpty()) {
            /** @phpstan-ignore-next-line Parameter #1 $callback of function call_user_func expects callable(): mixed, (callable(): mixed)|non-falsy-string given. */
            call_user_func($this->builder, $this);
        }

        return parent::render();
    }
}
