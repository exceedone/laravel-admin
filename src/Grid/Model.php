<?php

namespace Encore\Admin\Grid;

use Encore\Admin\Grid;
use Encore\Admin\Middleware\Pjax;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

/**
 * @method orderBy($column, $direction = 'asc');
 * @method where()
 * @method whereIn($column, $values, $boolean = 'and', $not = false)
 */
class Model
{
    /**
     * Eloquent model instance of the grid model.
     */
    protected $model;

    /**
     * @var EloquentModel
     */
    protected $originalModel;

    /**
     * Array of queries of the eloquent model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $queries;

    /**
     * Sort parameters of the model.
     *
     * @var array
     */
    protected $sort;

    /**
     * @var array
     */
    protected $data = [];

    /*
     * 20 items per page as default.
     *
     * @var int
     */
    protected $perPage = 20;

    /*
     * per page arguments.
     *
     * @var array
     */
    protected $perPageArguments = [];

    /*
     * handleInvalidPage. if false, disable call handleInvalidPage.
     *
     * @var boolean
     */
    protected $handleInvalidPage = true;

    /**
     * If the model use pagination.
     *
     * @var bool
     */
    protected $usePaginate = true;

    /**
     * The query string variable used to store the per-page.
     *
     * @var string
     */
    protected $perPageName = 'per_page';

    /**
     * The query string variable used to store the sort.
     *
     * @var string
     */
    protected $sortName = '_sort';

    /**
     * Collection callback.
     *
     * @var \Closure
     */
    protected $collectionCallback;

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var Relation
     */
    protected $relation;

    /**
     * @var array
     */
    protected $eagerLoads = [];

    /**
     * Create a new grid model instance.
     *
     * @param EloquentModel $model
     * @param Grid          $grid
     */
    public function __construct(EloquentModel $model, Grid $grid = null)
    {
        $this->model = $model;

        $this->originalModel = $model;

        $this->grid = $grid;

        $this->queries = collect();

//        static::doNotSnakeAttributes($this->model);
    }

    /**
     * Don't snake case attributes.
     *
     * @param EloquentModel $model
     *
     * @return void
     */
    protected static function doNotSnakeAttributes(EloquentModel $model)
    {
        $class = get_class($model);

        $class::$snakeAttributes = false;
    }

    /**
     * @return EloquentModel
     */
    public function getOriginalModel()
    {
        return $this->originalModel;
    }

    /**
     * Get the eloquent model of the grid model.
     *
     * @return EloquentModel
     */
    public function eloquent()
    {
        return $this->model;
    }

    /**
     * Enable or disable pagination.
     *
     * @param bool $use
     */
    public function usePaginate($use = true)
    {
        $this->usePaginate = $use;
    }

    /**
     * Get the query string variable used to store the per-page.
     *
     * @return string
     */
    public function getPerPageName()
    {
        return $this->perPageName;
    }

    /**
     * Set the query string variable used to store the per-page.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setPerPageName($name)
    {
        $this->perPageName = $name;

        return $this;
    }

    /**
     * Get per-page number.
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Set per-page number.
     *
     * @param int $perPage
     *
     * @return $this
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;

        $this->__call('paginate', [$perPage]);

        return $this;
    }

    /**
     * Set per-page arguments.
     * @param array $arguments
     * @return $this
     */
    public function setPerPageArguments($arguments)
    {
        $this->perPageArguments = $arguments;

        return $this;
    }

    /**
     * disable handleInvalidPage
     *
     * @return $this
     */
    public function disableHandleInvalidPage()
    {
        $this->handleInvalidPage = false;

        return $this;
    }

    /**
     * Get the query string variable used to store the sort.
     *
     * @return string
     */
    public function getSortName()
    {
        return $this->sortName;
    }

    /**
     * Set the query string variable used to store the sort.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setSortName($name)
    {
        $this->sortName = $name;

        return $this;
    }

    /**
     * Set parent grid instance.
     *
     * @param Grid $grid
     *
     * @return $this
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * Get parent gird instance.
     *
     * @return Grid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param Relation $relation
     *
     * @return $this
     */
    public function setRelation(Relation $relation)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @return Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Get constraints.
     *
     * @return array|bool
     */
    public function getConstraints()
    {
        if ($this->relation instanceof HasMany) {
            return [
                $this->relation->getForeignKeyName() => $this->relation->getParentKey(),
            ];
        }

        return false;
    }

    /**
     * Set collection callback.
     *
     * @param \Closure $callback
     *
     * @return $this
     */
    public function collection(\Closure $callback = null)
    {
        $this->collectionCallback = $callback;

        return $this;
    }

    /**
     * Build.
     *
     * @param bool $toArray
     *
     * @return array|Collection|mixed
     */
    public function buildData($toArray = true)
    {
        if (empty($this->data)) {
            $collection = $this->get();

            if ($this->collectionCallback) {
                $collection = call_user_func($this->collectionCallback, $collection);
            }

            if ($toArray) {
                $this->data = $collection->toArray();
            } else {
                $this->data = $collection;
            }
        }

        return $this->data;
    }

    /**
     * @param callable $callback
     * @param int      $count
     *
     * @return bool
     */
    public function chunk($callback, $count = 100)
    {
        if ($this->usePaginate) {
            return $this->buildData(false)->chunk($count)->each($callback);
        }

        $this->setSort();

        $this->queries->reject(function ($query) {
            return $query['method'] == 'paginate';
        })->each(function ($query) {
            if(isset($query['callback'])){
                $func = $query['callback'];
                $func($this->model, $query['arguments']);
            }
            else{
                $this->model = $this->model->{$query['method']}(...$query['arguments']);
            }
        });

        return $this->model->chunk($count, $callback);
    }

    /**
     * Add conditions to grid model.
     *
     * @param array $conditions
     *
     * @return $this
     */
    public function addConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            call_user_func_array([$this, key($condition)], current($condition));
        }

        return $this;
    }

    /**
     * Get table of the model.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->model->getTable();
    }

    /**
     * @throws \Exception
     */
    protected function get()
    {
        if ($this->model instanceof LengthAwarePaginator) {
            return $this->model;
        }

        if ($this->relation) {
            $this->model = $this->relation->getQuery();
        }

        $this->setSort();
        $this->setPaginate();

        $this->queries->unique()->each(function ($query) {
            if(isset($query['callback'])){
                $func = $query['callback'];
                $func($this->model, $query['arguments']);
            }
            else{
                $this->model = call_user_func_array([$this->model, $query['method']], $query['arguments']);
            }
        });

        if ($this->model instanceof Collection) {
            return $this->model;
        }

        if ($this->model instanceof LengthAwarePaginator) {
            if($this->handleInvalidPage){
                $this->handleInvalidPage($this->model);
            }

            return $this->model->getCollection();
        }

        throw new \Exception('Grid query error');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|EloquentModel
     */
    public function getQueryBuilder()
    {
        if ($this->relation) {
            return $this->relation->getQuery();
        }

        $this->setSort();

        $queryBuilder = $this->originalModel;

        $this->queries->reject(function ($query) {
            return in_array($query['method'], ['get', 'paginate']);
        })->each(function ($query) use (&$queryBuilder) {
            if(isset($query['callback'])){
                $func = $query['callback'];
                $func($queryBuilder, $query['arguments']);
            }
            else{
                $queryBuilder = $queryBuilder->{$query['method']}(...$query['arguments']);
            }
        });

        return $queryBuilder;
    }

    /**
     * If current page is greater than last page, then redirect to last page.
     *
     * @param LengthAwarePaginator $paginator
     *
     * @return void
     */
    protected function handleInvalidPage(LengthAwarePaginator $paginator)
    {
        if ($paginator->lastPage() && $paginator->currentPage() > $paginator->lastPage()) {
            $lastPageUrl = Request::fullUrlWithQuery([
                $paginator->getPageName() => $paginator->lastPage(),
            ]);

            Pjax::respond(redirect($lastPageUrl));
        }
    }

    /**
     * Set the grid paginate.
     *
     * @return void
     */
    protected function setPaginate()
    {
        $paginate = $this->findQueryByMethod('paginate');

        $this->queries = $this->queries->reject(function ($query) {
            return $query['method'] == 'paginate';
        });

        if (!$this->usePaginate) {
            $query = [
                'method'    => 'get',
                'arguments' => [],
            ];
        } else {
            $query = [
                'method'    => 'paginate',
                'arguments' => $this->resolvePerPage($paginate),
            ];
        }

        $this->queries->push($query);
    }

    /**
     * Resolve perPage for pagination.
     *
     * @param array|null $paginate
     *
     * @return array
     */
    protected function resolvePerPage($paginate)
    {
        if ($perPage = request($this->perPageName)) {
            if (is_array($paginate)) {
                $paginate['arguments'][0] = (int) $perPage;

                return $paginate['arguments'];
            }

            $this->perPage = (int) $perPage;
        }

        if(!empty($this->perPageArguments)){
            if(!isset($this->perPageArguments[0])){
                $this->perPageArguments[0] = $this->perPage;
            }
            return $this->perPageArguments;
        }

        if (isset($paginate['arguments'][0])) {
            return $paginate['arguments'];
        }

        if ($name = $this->grid->getName()) {
            return [$this->perPage, ['*'], "{$name}_page"];
        }

        return [$this->perPage];
    }

    /**
     * Find query by method name.
     *
     * @param $method
     *
     * @return static
     */
    protected function findQueryByMethod($method)
    {
        return $this->queries->first(function ($query) use ($method) {
            return $query['method'] == $method;
        });
    }

    /**
     * Set the grid sort.
     *
     * @return void
     */
    protected function setSort()
    {
        $this->sort = Request::get($this->sortName, []);
        if (!is_array($this->sort)) {
            return;
        }

        if (empty($this->sort['column']) || empty($this->sort['type'])) {
            return;
        }

        $column = $this->getSortColumn();
        // if sort as callback, Execute callback
        if($column && !is_null($column->getSortCallback())){
            $this->setCallbackSort();
            return;
        }

        $relationSort = false;
        if(boolval(Arr::get($this->sort, 'direct'))){
        }
        elseif (Str::contains($this->sort['column'], '.')) {
            $relationSort = true;
        }
        
        if($relationSort){
            $this->setRelationSort($this->sort['column']);
        }
        else {
            $this->resetOrderBy();

            // Change type -1 to desc, 1 to asc.
            $type = ($this->sort['type'] ?? 1) == -1 ? 'desc' : 'asc';
    
            // get column. if contains "cast", set set column as cast
            if ($column && !is_null($cast = $column->getCast())) {
                $columnName = \DB::getQueryGrammar()->wrap($this->sort['column']);
                $column = "CAST({$columnName} AS {$cast}) {$type}";
                $method = 'orderByRaw';
                $arguments = [$column];
            } else {
                $column = $this->sort['column'];
                $method = 'orderBy';
                $arguments = [$column, $type];
            }

            $this->queries->push([
                'method'    => $method,
                'arguments' => $arguments,
            ]);
        }
    }

    protected function getSortColumn(){
        $column_name = $this->sort['column'] ?? null;
        if(!$column_name){
            return null;
        }
        return $this->grid->columns()->first(function($column) use($column_name){
            if($column->getSortName() == $column_name){
                return true;
            }
            return false;
        });
    }

    /**
     * Set relation sort.
     *
     * @param string $column
     *
     * @return void
     */
    protected function setRelationSort($column)
    {
        list($relationName, $relationColumn) = explode('.', $column);

        if ($this->queries->contains(function ($query) use ($relationName) {
            return $query['method'] == 'with' && in_array($relationName, $query['arguments']);
        })) {
            $relation = $this->model->$relationName();

            $this->queries->push([
                'method'    => 'select',
                'arguments' => [$this->model->getTable().'.*'],
            ]);

            $this->queries->push([
                'method'    => 'join',
                'arguments' => $this->joinParameters($relation),
            ]);

            $this->resetOrderBy();

            // Change type -1 to desc, 1 to asc.
            $type = ($this->sort['type'] ?? 1) == -1 ? 'desc' : 'asc';
    
            $this->queries->push([
                'method'    => 'orderBy',
                'arguments' => [
                    $relation->getRelated()->getTable().'.'.$relationColumn,
                    $type,
                ],
            ]);
        }
    }

    /**
     * Set callback sort.
     * @return false|void
     */
    protected function setCallbackSort()
    {
        $column = $this->getSortColumn();
        if($column && !is_null($func = $column->getSortCallback())){
            $this->resetOrderBy();   

            // Change type -1 to desc, 1 to asc.
            $type = ($this->sort['type'] ?? 1) == -1 ? 'desc' : 'asc';
    
            // call callback sorting.
            $this->queries->push([
                'method' => null,
                'callback' => $func,
                'arguments' => [$type],
            ]);
            return false;
        }
    }

    /**
     * Reset orderBy query.
     *
     * @return void
     */
    public function resetOrderBy()
    {
        $this->queries = $this->queries->reject(function ($query) {
            return $query['method'] == 'orderBy' || $query['method'] == 'orderByDesc';
        });
    }

    /**
     * Build join parameters for related model.
     *
     * `HasOne` and `BelongsTo` relation has different join parameters.
     *
     * @param Relation $relation
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function joinParameters(Relation $relation)
    {
        $relatedTable = $relation->getRelated()->getTable();

        if ($relation instanceof BelongsTo) {
            $foreignKeyMethod = version_compare(app()->version(), '5.8.0', '<') ? 'getForeignKey' : 'getForeignKeyName';

            return [
                $relatedTable,
                $relation->{$foreignKeyMethod}(),
                '=',
                $relatedTable.'.'.$relation->getRelated()->getKeyName(),
            ];
        }

        if ($relation instanceof HasOne) {
            return [
                $relatedTable,
                $relation->getQualifiedParentKeyName(),
                '=',
                $relation->getQualifiedForeignKeyName(),
            ];
        }

        throw new \Exception('Related sortable only support `HasOne` and `BelongsTo` relation.');
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $match = false;

        // if matched method name, update
        $this->queries = $this->queries->map(function($query) use($method, $arguments, &$match){
            // rewrite value target methods
            $rewriteTargets = ['paginate'];
            
            if(is_string($method) && $query['method'] == $method && in_array($method, $rewriteTargets)){
                $query['arguments'] = $arguments;
                $match = true;
            }

            return $query;
        });

        if(!$match){
            $this->queries->push([
                'method'    => $method,
                'arguments' => $arguments,
            ]);
        }

        return $this;
    }

    /**
     * Set the relationships that should be eager loaded.
     *
     * @param mixed $relations
     *
     * @return $this|Model
     */
    public function with($relations)
    {
        if (is_array($relations)) {
            if (Arr::isAssoc($relations)) {
                $relations = array_keys($relations);
            }

            $this->eagerLoads = array_merge($this->eagerLoads, $relations);
        }

        if (is_string($relations)) {
            if (Str::contains($relations, '.')) {
                $relations = explode('.', $relations)[0];
            }

            if (Str::contains($relations, ':')) {
                $relations = explode(':', $relations)[0];
            }

            if (in_array($relations, $this->eagerLoads)) {
                return $this;
            }

            $this->eagerLoads[] = $relations;
        }

        return $this->__call('with', (array) $relations);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        $data = $this->buildData();

        if (array_key_exists($key, $data)) {
            return $data[$key];
        }
    }
}
