<?php

namespace Encore\Admin\Widgets\Grid\Displayers;

use Illuminate\Support\Arr;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Linker;

class Actions extends AbstractDisplayer
{
    /**
     * @var array
     */
    protected $appends = [];

    /**
     * @var array
     */
    protected $prepends = [];

    /**
     * Default actions.
     *
     * @var array
     */
    protected $actions = ['view', 'edit', 'delete'];

    /**
     * @var string
     */
    protected $resource;

    /**
     * Append a action.
     *
     * @param $action
     *
     * @return $this
     */
    public function append($action)
    {
        array_push($this->appends, $action);

        return $this;
    }

    /**
     * Prepend a action.
     *
     * @param $action
     *
     * @return $this
     */
    public function prepend($action)
    {
        array_unshift($this->prepends, $action);

        return $this;
    }

    /**
     * Get route key name of current row.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return Arr::get((array)$this->row, $this->grid->getKeyName());
    }

    /**
     * Disable view action.
     *
     * @return $this
     */
    public function disableView(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->actions, 'view');
        } elseif (!in_array('view', $this->actions)) {
            array_push($this->actions, 'view');
        }

        return $this;
    }

    /**
     * Disable delete.
     *
     * @return $this.
     */
    public function disableDelete(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->actions, 'delete');
        } elseif (!in_array('delete', $this->actions)) {
            array_push($this->actions, 'delete');
        }

        return $this;
    }

    /**
     * Disable edit.
     *
     * @return $this.
     */
    public function disableEdit(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->actions, 'edit');
        } elseif (!in_array('edit', $this->actions)) {
            array_push($this->actions, 'edit');
        }

        return $this;
    }

    /**
     * Set resource of current resource.
     *
     * @param $resource
     *
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource of current resource.
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource ?: parent::getResource();
    }

    /**
     * {@inheritdoc}
     */
    public function display($callback = null)
    {
        if ($callback instanceof \Closure) {
            $callback->call($this, $this);
        }

        $actions = $this->prepends;

        foreach ($this->actions as $action) {
            $method = 'render'.ucfirst($action);
            array_push($actions, $this->{$method}());
        }

        $actions = array_merge($actions, $this->appends);

        return implode('', $actions);
    }

    /**
     * Render view action.
     *
     * @return string
     */
    protected function renderView()
    {
        $uri = url($this->getResource());

        return (new Linker)->url("{$uri}/{$this->getRouteKey()}")->icon('fa-eye')->tooltip(trans('admin.show'))
            ->linkattributes(['class' => "{$this->grid->getGridRowName()}-view"])
            ;
    }

    /**
     * Render edit action.
     *
     * @return string
     */
    protected function renderEdit()
    {
        $uri = url($this->getResource());
        return (new Linker)->url("{$uri}/{$this->getRouteKey()}/edit")->icon('fa-edit')->tooltip(trans('admin.edit'))
            ->linkattributes(['class' => "{$this->grid->getGridRowName()}-edit"])
            ;
    }

    /**
     * Render delete action.
     *
     * @return string
     */
    protected function renderDelete()
    {
        $this->setupDeleteScript();

        return (new Linker)
            ->script(true)
            ->linkattributes(['class' => "{$this->grid->getGridRowName()}-delete", 'data-id' => $this->getKey() ])
            ->icon('fa-trash')
            ->tooltip(trans('admin.delete'));
    }

    protected function setupDeleteScript()
    {
        $uri = url($this->getResource());
        $trans = [
            'delete_confirm' => trans('admin.delete_confirm'),
            'confirm'        => trans('admin.confirm'),
            'cancel'         => trans('admin.cancel'),
        ];

        $script = <<<SCRIPT

$('.{$this->grid->getGridRowName()}-delete').unbind('click').click(function() {

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
                    url: '{$uri}/' + id,
                    data: {
                        _method:'delete',
                        _token:LA.token,
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');

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

SCRIPT;

        Admin::script($script);
    }
}
