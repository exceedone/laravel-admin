<?php

namespace OpenAdmin\Admin;

use Closure;
use OpenAdmin\Admin\Auth\Database\Menu;
use OpenAdmin\Admin\Traits\ModelTree;
use OpenAdmin\Admin\Tree\Tools;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;

class Tree implements Renderable
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var array<mixed>
     */
    protected $items = [];

    /**
     * @var string
     */
    protected $elementId = 'tree-';

    /**
     * @var Model|null
     */
    protected $model;

    /**
     * @var \Closure
     */
    protected $queryCallback;

    /**
     * @var \Closure
     */
    protected $getCallback;

    /**
     * View of tree to render.
     *
     * @var array<string, string>
     */
    protected $view = [
        'tree'   => 'admin::tree',
        'branch' => 'admin::tree.branch',
    ];

    /**
     * @var \Closure
     */
    protected $callback;

    /**
     * @var \Closure|null
     */
    protected $branchCallback = null;

    /**
     * @var bool
     */
    public $useExpandCollapse = true;

    /**
     * @var bool
     */
    public $useCreate = true;

    /**
     * @var bool
     */
    public $useSave = true;

    /**
     * @var bool
     */
    public $useRefresh = true;

    /**
     * @var bool
     */
    public $useAction = true;

    /**
     * @var bool
     */
    public $useNestable = true;

    /**
     * @var array<mixed>
     */
    protected $nestableOptions = [];

    /**
     * Header tools.
     *
     * @var Tools
     */
    public $tools;

    /**
     * @var string
     */
    public $path;

    /**
     * Menu constructor.
     *
     * @param Model|null $model
     */
    public function __construct(Model $model = null, \Closure $callback = null)
    {
        $this->model = $model;

        $this->path = url(app('request')->getPathInfo());
        $this->elementId .= uniqid();

        $this->setupTools();

        if ($callback instanceof \Closure) {
            call_user_func($callback, $this);
        }

        $this->initBranchCallback();
    }

    /**
     * Setup tree tools.
     *
     * @return void
     */
    public function setupTools()
    {
        $this->tools = new Tools($this);
    }

    /**
     * Initialize branch callback.
     *
     * @return void
     */
    protected function initBranchCallback()
    {
        if (is_null($this->branchCallback)) {
            $this->branchCallback = function ($branch) {
                $key = $branch[$this->model->getKeyName()];
                /** @phpstan-ignore-next-line Call to an undefined method Illuminate\Database\Eloquent\Model::getTitleColumn(). */
                $title = $branch[$this->model->getTitleColumn()];

                return "$key - $title";
            };
        }
    }

    /**
     * Set branch callback.
     *
     * @param \Closure $branchCallback
     *
     * @return $this
     */
    public function branch(\Closure $branchCallback)
    {
        $this->branchCallback = $branchCallback;

        return $this;
    }

    /**
     * Set query callback this tree.
     *
     * @return $this
     */
    public function query(\Closure $callback)
    {
        $this->queryCallback = $callback;

        return $this;
    }

    /**
     * Set get callback to model.
     * @param Closure|null $get
     * @return $this
     */
    public function getCallback(\Closure $get = null)
    {
        $this->getCallback = $get;

        return $this;
    }

    /**
     * Set title
     * @param string $title
     *
     * @return void
     */
    public function title($title)
    {
        $this->title = $title;
    }

    /**
     * Set nestable options.
     *
     * @param array<mixed> $options
     *
     * @return $this
     */
    public function nestable($options = [])
    {
        $this->nestableOptions = array_merge($this->nestableOptions, $options);

        return $this;
    }

    /**
     * Disable ExpandCollapse.
     *
     * @return void
     */
    public function disableExpandCollapse()
    {
        $this->useExpandCollapse = false;
    }

    /**
     * Disable create.
     *
     * @return void
     */
    public function disableCreate()
    {
        $this->useCreate = false;
    }

    /**
     * Disable save.
     *
     * @return void
     */
    public function disableSave()
    {
        $this->useSave = false;
    }

    /**
     * Disable refresh.
     *
     * @return void
     */
    public function disableRefresh()
    {
        $this->useRefresh = false;
    }

    /**
     * Disable refresh.
     *
     * @return void
     */
    public function disableAction()
    {
        $this->useAction = false;
    }

    /**
     * Disable Nestable event.
     *
     * @return void
     */
    public function disableNestable()
    {
        $this->useNestable = false;
    }

    /**
     * Save tree order from a input.
     *
     * @param string $serialize
     *
     * @return bool
     */
    public function saveOrder($serialize)
    {
        $tree = json_decode($serialize, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        /** @phpstan-ignore-next-line Call to an undefined method Illuminate\Database\Eloquent\Model::saveOrder(). */
        $this->model->saveOrder($tree);

        return true;
    }

    /**
     * Build tree grid scripts.
     *
     * @return string
     */
    protected function script()
    {
        $trans = [
            'delete_confirm'    => trans('admin.delete_confirm'),
            'save_succeeded'    => trans('admin.save_succeeded'),
            'refresh_succeeded' => trans('admin.refresh_succeeded'),
            'delete_succeeded'  => trans('admin.delete_succeeded'),
            'confirm'           => trans('admin.confirm'),
            'cancel'            => trans('admin.cancel'),
        ];

        $nestableOptions = json_encode($this->nestableOptions);

        $useNestable = $this->useNestable ? 'true' : 'false';
        return <<<SCRIPT

        if({$useNestable}){
            $('#{$this->elementId}').nestable($nestableOptions);
        }

        $('.tree_branch_delete').click(function() {
            var id = $(this).data('id');
            swal({
                title: "{$trans['delete_confirm']}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{$trans['confirm']}",
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                cancelButtonText: "{$trans['cancel']}",
                preConfirm: function() {
                    $('.swal2-cancel').hide();
                    return new Promise(function(resolve) {
                        $.ajax({
                            method: 'post',
                            url: '{$this->path}/' + id,
                            data: {
                                _method:'delete',
                                _token:LA.token,
                            },
                            success: function (data) {
                                $.pjax.reload('#pjax-container');
                                toastr.success('{$trans['delete_succeeded']}');
                                resolve(data);
                            }
                        });
                    });
                }
            }).then(function(result) {
                var data = result.value;
                if (typeof data === 'object') {
                    if (data.status) {
                        swal(data.message, '', 'success');
                    } else {
                        swal(data.message, '', 'error');
                    }
                }
            });
        });

        $('.{$this->elementId}-save').click(function () {
            var serialize = $('#{$this->elementId}').nestable('serialize');

            $.post('{$this->path}', {
                _token: LA.token,
                _order: JSON.stringify(serialize)
            },
            function(data){
                $.pjax.reload('#pjax-container');
                toastr.success('{$trans['save_succeeded']}');
            });
        });

        $('.{$this->elementId}-refresh').click(function () {
            $.pjax.reload('#pjax-container');
            toastr.success('{$trans['refresh_succeeded']}');
        });

        $('.{$this->elementId}-tree-tools').on('click', function(e){
            var action = $(this).data('action');
            if (action === 'expand') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse') {
                $('.dd').nestable('collapseAll');
            }
        });


SCRIPT;
    }

    /**
     * Set view of tree.
     *
     * @param array<string, string> $view
     *
     * @return void
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * Return all items of the tree.
     * @return mixed
     */
    public function getItems()
    {
        /** @phpstan-ignore-next-line Call to an undefined method Illuminate\Database\Eloquent\Model::withQuery(). */
        return $this->model
            ->withQuery($this->queryCallback)
            ->getCallback($this->getCallback)
            ->toTree();
    }

    /**
     * Variables in tree template.
     *
     * @return array<string, mixed>
     */
    public function variables()
    {
        return [
            'id'         => $this->elementId,
            'title'      => $this->title,
            'tools'      => $this->tools->render(),
            'items'      => $this->getItems(),
            'useCreate'  => $this->useCreate,
            'useSave'    => $this->useSave,
            'useRefresh' => $this->useRefresh,
            'useAction' => $this->useAction,
            'useExpandCollapse' => $this->useExpandCollapse,
        ];
    }

    /**
     * Setup grid tools.
     *
     * @param Closure $callback
     *
     * @return void
     */
    public function tools(Closure $callback)
    {
        call_user_func($callback, $this->tools);
    }

    /**
     * Render a tree.
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function render()
    {
        Admin::script($this->script());

        view()->share([
            'path'           => $this->path,
            'keyName'        => $this->model->getKeyName(),
            'branchView'     => $this->view['branch'],
            'branchCallback' => $this->branchCallback,
            'useAction' => $this->useAction,
        ]);

        return view($this->view['tree'], $this->variables())->render();
    }

    /**
     * Get the string contents of the grid view.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
