<?php

namespace Encore\Admin\Form;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Encore\Admin\Form\Field\Hidden;
use Encore\Admin\Grid\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Class Builder.
 */
class Builder
{
    /**
     * @var string
     */
    public static $footerClassName = \Encore\Admin\Form\Footer::class;

    /**
     *  Previous url key.
     */
    const PREVIOUS_URL_KEY = '_previous_';
    const FORM_ID = 'formid';
    const REDIRECT_DASHBOARD = 'redirect-dashboard';
    const REDIRECT_CAMERA = 'redirect-camera';

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var Form|null
     */
    protected $form;

    /**
     * @var mixed
     */
    protected $action;

    /**
     * @var Collection<int|string, mixed>|null
     */
    protected $fields;

    /**
     * @var array<mixed>
     */
    protected $options = [];

    /**
     * Modes constants.
     */
    const MODE_EDIT = 'edit';
    const MODE_CREATE = 'create';

    /**
     * Form action mode, could be create|view|edit.
     *
     * @var string
     */
    protected $mode = 'create';

    /**
     * @var array<mixed>
     */
    protected $attributes = [];

    /**
     * @var array<mixed>
     */
    protected $hiddenFields = [];

    /**
     * @var Tools
     */
    protected $tools;

    /**
     * @var Footer
     */
    protected $footer;

    /**
     * Width for label and field.
     *
     * @var array<string, int>
     */
    protected $width = [
        'label' => 2,
        'field' => 8,
    ];

    /**
     * View for this form.
     *
     * @var string
     */
    protected $view = 'admin::form';

    /**
     * Form title.
     *
     * @var string
     */
    protected $title;

    /**
     * Whether disable pjax
     *
     * @var bool
     */
    protected $disablePjax = false;

    /**
     * Whether disable validate
     *
     * @var bool
     */
    protected $disableValidate = false;

    /**
     * Builder constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;

        $this->fields = new Collection();

        $this->init();
    }

    /**
     * Do initialize.
     *
     * @return void
     */
    public function init()
    {
        $this->tools = new Tools($this);
        $this->footer = new static::$footerClassName($this);
    }

    /**
     * Get form tools instance.
     *
     * @return Tools
     */
    public function getTools()
    {
        return $this->tools;
    }

    /**
     * Get form footer instance.
     *
     * @return Footer
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Set the builder mode.
     *
     * @param string $mode
     *
     * @return void
     */
    public function setMode($mode = 'create')
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Returns builder is $mode.
     *
     * @param string $mode
     *
     * @return bool
     */
    public function isMode($mode)
    {
        return $this->mode == $mode;
    }

    /**
     * Check if is creating resource.
     *
     * @return bool
     */
    public function isCreating()
    {
        return $this->isMode(static::MODE_CREATE);
    }

    /**
     * Check if is editing resource.
     *
     * @return bool
     */
    public function isEditing()
    {
        return $this->isMode(static::MODE_EDIT);
    }

    /**
     * Set resource Id.
     *
     * @param \Illuminate\Database\Eloquent\Model|mixed $id
     * @return $this
     */
    public function setResourceId($id)
    {
        if($id instanceof \Illuminate\Database\Eloquent\Model){
            $this->id = $id->id;
        }else{
            $this->id = $id;
        }

        return $this;
    }

    /**
     * Get Resource id.
     *
     * @return mixed
     */
    public function getResourceId()
    {
        return $this->id;
    }

    /**
     * @param int|null $slice
     * @return string
     */
    public function getResource($slice = null)
    {
        if ($this->mode == self::MODE_CREATE) {
            return $this->form->resource(-1);
        }
        if ($slice !== null) {
            return $this->form->resource($slice);
        }

        return $this->form->resource();
    }

    /**
     * Disable Pjax.
     *
     * @return $this
     */
    public function disablePjax()
    {
        $this->disablePjax = true;
        \Admin::disablePjax();

        return $this;
    }

    /**
     * Disable Validate.
     *
     * @return $this
     */
    public function disableValidate()
    {
        $this->disableValidate = true;

        return $this;
    }

    /**
     * @param int $field
     * @param int $label
     *
     * @return $this
     */
    public function setWidth($field = 8, $label = 2)
    {
        $this->width = [
            'label' => $label,
            'field' => $field,
        ];

        return $this;
    }

    /**
     * Get label and field width.
     *
     * @return array<mixed>
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set form action.
     *
     * @param string $action
     *
     * @return void
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get Form action.
     *
     * @return string
     */
    public function getAction()
    {
        if ($this->action) {
            return $this->action;
        }

        if ($this->isMode(static::MODE_EDIT)) {
            return $this->form->resource().'/'.$this->id;
        }

        if ($this->isMode(static::MODE_CREATE)) {
            return $this->form->resource(-1);
        }

        return '';
    }

    /**
     * Set view for this form.
     *
     * @param string $view
     *
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Set title for form.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get fields of this builder.
     *
     * @return Collection<int|string, mixed>
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Get specify field.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function field($name)
    {
        return $this->fields()->first(function (Field $field) use ($name) {
            return $field->column() == $name;
        });
    }

    /**
     * If the parant form has rows.
     *
     * @return bool
     */
    public function hasRows()
    {
        return !empty($this->form->rows);
    }

    /**
     * Get field rows of form.
     *
     * @return array<mixed>
     */
    public function getRows()
    {
        return $this->form->rows;
    }

    /**
     * @return array<mixed>
     */
    public function getHiddenFields()
    {
        return $this->hiddenFields;
    }

    /**
     * @param Field $field
     *
     * @return void
     */
    public function addHiddenField(Field $field)
    {
        $this->hiddenFields[] = $field;
    }

    /**
     * Add or get options.
     *
     * @param array<mixed> $options
     *
     * @return array<mixed>|null
     */
    public function options($options = [])
    {
        if (empty($options)) {
            return $this->options;
        }

        $this->options = array_merge($this->options, $options);
    }

    /**
     * Get or set option.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return $this
     */
    public function option($option, $value = null)
    {
        if (func_num_args() == 1) {
            return Arr::get($this->options, $option);
        }

        $this->options[$option] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function title()
    {
        if ($this->title) {
            return $this->title;
        }

        if ($this->mode == static::MODE_CREATE) {
            return trans('admin.create');
        }

        if ($this->mode == static::MODE_EDIT) {
            return trans('admin.edit');
        }

        return '';
    }

    /**
     * Determine if form fields has files.
     *
     * @return bool
     */
    public function hasFile()
    {
        foreach ($this->fields() as $field) {
            if(method_exists($field, 'hasFile')){
                if($field->hasFile()){
                    return true;
                }
            }
            elseif ($field instanceof Field\File || $field instanceof Field\MultipleFile) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add field for store redirect url after update or store.
     *
     * @return void
     */
    protected function addRedirectUrlField()
    {
        $previous = URL::previous();
        $formid = request()->get('formid');
        $redirectDashboard = request()->get('redirect-dashboard');
        $redirectCamera = request()->get('redirect-camera');

        if ($formid) {
            $this->addHiddenField((new Hidden(static::FORM_ID))->value($formid));
        }
        if ($redirectDashboard) {
            $this->addHiddenField((new Hidden(static::REDIRECT_DASHBOARD))->value($redirectDashboard));
        }
        if ($redirectCamera) {
            $this->addHiddenField((new Hidden(static::REDIRECT_CAMERA))->value($redirectCamera));
        }
        if (!$previous || $previous == URL::current()) {
            return;
        }

        if (Str::contains($previous, url($this->getResource()))) {
            $this->addHiddenField((new Hidden(static::PREVIOUS_URL_KEY))->value($previous));
        }
    }

    /**
     * Open up a new HTML form.
     *
     * @param array<mixed> $options
     *
     * @return string
     */
    public function open($options = [])
    {
        // set atribute
        $this->form->attribute($options);

        if ($this->isMode(self::MODE_EDIT)) {
            $this->addHiddenField((new Hidden('_method'))->value('PUT'));
        }

        $this->addRedirectUrlField();

        $this->form->attribute([
            'action' => url($this->getAction()),
            'method' => Arr::get($options, 'method', 'post'),
            'accept-charset' => 'UTF-8',
            'data-form_uniquename' => $this->form->getUniqueName(),
            'class' => $this->form->getUniqueName(),
        ]);
        
        if($this->disableValidate){
            $this->form->attribute('novalidate', 1);
        }
        if ($this->hasFile()) {
            $this->form->attribute('enctype', 'multipart/form-data');
        }

        $html = [];
        foreach ($this->form->getAttributes() as $name => $value) {
            $html[] = "$name=\"$value\"";
        }

        return '<form '.implode(' ', $html).' ' . ($this->disablePjax ? '' : 'pjax-container') . '>';
    }

    /**
     * Close the current form.
     *
     * @return string
     */
    public function close()
    {
        $this->form = null;
        $this->fields = null;

        return '</form>';
    }

    /**
     * Remove reserved fields like `id` `created_at` `updated_at` in form fields.
     *
     * @return void
     */
    protected function removeReservedFields()
    {
        if (!$this->isMode(static::MODE_CREATE)) {
            return;
        }

        $reservedColumns = [
            $this->form->model()->getKeyName(),
            $this->form->model()->getCreatedAtColumn(),
            $this->form->model()->getUpdatedAtColumn(),
        ];

        $this->fields = $this->fields()->reject(function (Field $field) use ($reservedColumns) {
            return in_array($field->column(), $reservedColumns);
        });
    }

    /**
     * Render form header tools.
     *
     * @return string
     */
    public function renderTools()
    {
        return $this->tools->render();
    }

    /**
     * Render form footer.
     *
     * @return string
     */
    public function renderFooter()
    {
        return $this->footer->render();
    }

    /**
     * Render form.
     *
     * @return string
     */
    public function render()
    {
        $this->removeReservedFields();

        $tabObj = $this->form->getTab();

        if (!$tabObj->isEmpty()) {
            $script = <<<'SCRIPT'

var hash = document.location.hash;
if (hash) {
    $('.nav-tabs a[href="' + hash + '"]').tab('show');
}

// Change hash for page-reload
$('.nav-tabs a').on('shown.bs.tab', function (e) {
    history.pushState(null,null, e.target.hash);
});

if ($('.has-error').length) {
    $('.has-error').each(function () {
        var tabId = '#'+$(this).closest('.tab-pane').attr('id');
        $('li a[href="'+tabId+'"] i').removeClass('hide');
    });

    var first = $('.has-error:first').closest('.tab-pane').attr('id');
    $('li a[href="#'+first+'"]').tab('show');
}

SCRIPT;
            Admin::script($script);
        }

        $data = [
            'form'   => $this,
            'tabObj' => $tabObj,
            'width'  => $this->width,
        ];

        return view($this->view, $data)->render();
    }
}
