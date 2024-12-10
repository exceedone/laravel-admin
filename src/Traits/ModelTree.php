<?php

namespace OpenAdmin\Admin\Traits;

use OpenAdmin\Admin\Auth\Database\Menu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

trait ModelTree
{
    /**
     * @var array<mixed>
     */
    protected static $branchOrder = [];

    /**
     * @var string
     */
    protected $parentColumn = 'parent_id';

    /**
     * @var string
     */
    protected $titleColumn = 'title';

    /**
     * @var string
     */
    protected $orderColumn = 'order';

    /**
     * @var \Closure
     */
    protected $queryCallback;

    /**
     * @var \Closure
     */
    protected $getCallback;

    /**
     * Get children of current node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<static>
     */
    public function children()
    {
        return $this->hasMany(static::class, $this->parentColumn);
    }

    /**
     * Get parent of current node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<static, static>
     */
    public function parent()
    {
        return $this->belongsTo(static::class, $this->parentColumn);
    }

    /**
     * @return string
     */
    public function getParentColumn()
    {
        return $this->parentColumn;
    }

    /**
     * Set parent column.
     *
     * @param string $column
     *
     * @return void
     */
    public function setParentColumn($column)
    {
        $this->parentColumn = $column;
    }

    /**
     * Get title column.
     *
     * @return string
     */
    public function getTitleColumn()
    {
        return $this->titleColumn;
    }

    /**
     * Set title column.
     *
     * @param string $column
     *
     * @return void
     */
    public function setTitleColumn($column)
    {
        $this->titleColumn = $column;
    }

    /**
     * Get order column name.
     *
     * @return string
     *
     * @return string
     */
    public function getOrderColumn()
    {
        return $this->orderColumn;
    }

    /**
     * Set order column.
     *
     * @param string $column
     *
     * @return void
     */
    public function setOrderColumn($column)
    {
        $this->orderColumn = $column;
    }

    /**
     * Set query callback to model.
     *
     * @param \Closure|null $query
     *
     * @return $this
     */
    public function withQuery(\Closure $query = null)
    {
        $this->queryCallback = $query;

        return $this;
    }

    /**
     * Set get callback to model.
     * @param \Closure|null $get
     * @return $this
     */
    public function getCallback(\Closure $get = null)
    {
        $this->getCallback = $get;

        return $this;
    }

    /**
     * Format data to tree like array.
     *
     * @return array<mixed>
     */
    public function toTree()
    {
        return $this->buildNestedArray();
    }

    /**
     * Build Nested array.
     *
     * @param array<mixed> $nodes
     * @param int   $parentId
     *
     * @return array<mixed>
     */
    protected function buildNestedArray(array $nodes = [], $parentId = 0)
    {
        $branch = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $node) {
            if ($node[$this->parentColumn] == $parentId) {
                $children = $this->buildNestedArray($nodes, $node[$this->getKeyName()]);

                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }

    /**
     * Get all elements.
     *
     * @return mixed
     */
    public function allNodes()
    {
        $orderColumn = DB::getQueryGrammar()->wrap($this->orderColumn);
        $byOrder = $orderColumn.' = 0,'.$orderColumn;

        $self = new static();

        if ($this->queryCallback instanceof \Closure) {
            $self = call_user_func($this->queryCallback, $self);
        }

        $items = $self->orderByRaw($byOrder)->get();
        
        if ($this->getCallback instanceof \Closure) {
            $items = call_user_func($this->getCallback, $items);
        }

        return $items->toArray();
    }

    /**
     * Set the order of branches in the tree.
     *
     * @param array<mixed> $order
     *
     * @return void
     */
    protected static function setBranchOrder(array $order)
    {
        static::$branchOrder = array_flip(Arr::flatten($order));

        static::$branchOrder = array_map(function ($item) {
            return ++$item;
        }, static::$branchOrder);
    }

    /**
     * Save tree order from a tree like array.
     *
     * @param array<mixed> $tree
     * @param int   $parentId
     *
     * @return void
     */
    public static function saveOrder($tree = [], $parentId = 0)
    {
        if (empty(static::$branchOrder)) {
            static::setBranchOrder($tree);
        }

        foreach ($tree as $branch) {
            $node = static::find($branch['id']);

            $node->{$node->getParentColumn()} = $parentId;
            $node->{$node->getOrderColumn()} = static::$branchOrder[$branch['id']];
            $node->save();

            if (isset($branch['children'])) {
                static::saveOrder($branch['children'], $branch['id']);
            }
        }
    }

    /**
     * Get options for Select field in form.
     *
     * @param \Closure|null $closure
     * @param string        $rootText
     *
     * @return array<mixed>
     */
    public static function selectOptions(\Closure $closure = null, $rootText = 'ROOT')
    {
        $options = (new static())->withQuery($closure)->buildSelectOptions();

        return collect($options)->prepend($rootText, 0)->all();
    }

    /**
     * Build options of select field in form.
     *
     * @param array<mixed>  $nodes
     * @param int    $parentId
     * @param string $prefix
     * @param string $space
     *
     * @return array<mixed>
     */
    protected function buildSelectOptions(array $nodes = [], $parentId = 0, $prefix = '', $space = '&nbsp;')
    {
        $prefix = $prefix ?: '┝'.$space;

        $options = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $index => $node) {
            if ($node[$this->parentColumn] == $parentId) {
                $node[$this->titleColumn] = $prefix.$space.$node[$this->titleColumn];

                $childrenPrefix = str_replace('┝', str_repeat($space, 6), $prefix).'┝'.str_replace(['┝', $space], '', $prefix);

                $children = $this->buildSelectOptions($nodes, $node[$this->getKeyName()], $childrenPrefix);

                $options[$node[$this->getKeyName()]] = $node[$this->titleColumn];

                if ($children) {
                    $options += $children;
                }
            }
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool|null
     */
    public function delete()
    {
        $this->where($this->parentColumn, $this->getKey())->delete();

        return parent::delete();
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function (Model $branch) {
            /** @var Menu $menu */
            $menu = $branch;
            $parentColumn = $menu->getParentColumn();

            if (Request::has($parentColumn) && Request::input($parentColumn) == $branch->getKey()) {
                throw new \Exception(trans('admin.parent_select_error'));
            }

            if (Request::has('_order')) {
                $order = Request::input('_order');

                Request::offsetUnset('_order');

                static::tree()->saveOrder($order);

                return false;
            }

            return $branch;
        });
    }
}
