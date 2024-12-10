<?php

namespace OpenAdmin\Admin\Widgets;

use Closure;
use OpenAdmin\Admin\Form as BaseForm;
use OpenAdmin\Admin\Form\Field;
use OpenAdmin\Admin\Traits\FormTrait;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;

/**
 * Class Form.
 *
 * @method Field\Text           text($name, $label = '')
 * @method Field\Password       password($name, $label = '')
 * @method Field\Checkbox       checkbox($name, $label = '')
 * @method Field\Radio          radio($name, $label = '')
 * @method Field\Select         select($name, $label = '')
 * @method Field\MultipleSelect multipleSelect($name, $label = '')
 * @method Field\Textarea       textarea($name, $label = '')
 * @method Field\Hidden         hidden($name, $label = '')
 * @method Field\Id             id($name, $label = '')
 * @method Field\Ip             ip($name, $label = '')
 * @method Field\Url            url($name, $label = '')
 * @method Field\Color          color($name, $label = '')
 * @method Field\Email          email($name, $label = '')
 * @method Field\Mobile         mobile($name, $label = '')
 * @method Field\Slider         slider($name, $label = '')
 * @method Field\File           file($name, $label = '')
 * @method Field\Image          image($name, $label = '')
 * @method Field\Date           date($name, $label = '')
 * @method Field\Datetime       datetime($name, $label = '')
 * @method Field\Time           time($name, $label = '')
 * @method Field\Year           year($column, $label = '')
 * @method Field\Month          month($column, $label = '')
 * @method Field\DateRange      dateRange($start, $end, $label = '')
 * @method Field\DateTimeRange  dateTimeRange($start, $end, $label = '')
 * @method Field\TimeRange      timeRange($start, $end, $label = '')
 * @method Field\Number         number($name, $label = '')
 * @method Field\Currency       currency($name, $label = '')
 * @method Field\SwitchField    switch($name, $label = '')
 * @method Field\Display        display($name, $label = '')
 * @method Field\Rate           rate($name, $label = '')
 * @method Field\Divider        divider($title = '')
 * @method Field\Decimal        decimal($column, $label = '')
 * @method Field\Html           html($html)
 * @method Field\Tags           tags($column, $label = '')
 * @method Field\Icon           icon($column, $label = '')
 * @method Field\Captcha        captcha($column, $label = '')
 * @method Field\Listbox        listbox($column, $label = '')
 * @method Field\Table          table($column, $label, $builder)
 * @method Field\Timezone       timezone($column, $label = '')
 * @method Field\KeyValue       keyValue($column, $label = '')
 * @method Field\ListField      list($column, $label = '')
 * @method mixed                handle(Request $request)
 */
class Form implements Renderable
{
    use FormTrait;

    /**
     * The title of form.
     *
     * @var string
     */
    public $title;

    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * @var array<mixed>
     */
    protected $data = [];

    /**
     * Submit label.
     *
     * @var string|null
     */
    protected $submitLabel;

    /**
     * Available buttons.
     *
     * @var array<string>
     */
    protected $buttons = ['reset', 'submit'];

    /**
     * Available footer checks.
     * 
     * $submitRedirects : [
     *     [
     *         'key': 'list', // this check key name. Use default check etc
     *         'value': 'foo', // this check value name
     *         'label': 'FOO', // this check label
     *         'default': true, // if this flow is checked, set true
     *     ],
     *     [
     *         'key': 'edit', // this check key name. Use default check etc
     *         'value': 'bar', // this check value name
     *         'label': 'BAR', // this check label
     *         'default': true, // if this flow is checked, set true
     *     ],
     * ]
     *
     * @var array<mixed>
     */
    protected $submitRedirects = [];

    /**
     * Default Submit label.
     *
     * @var string|null
     */
    public static $defaultSubmitLabel;

    /**
     * Width for label and submit field.
     *
     * @var array<string,int>
     */
    protected $width = [
        'label' => 2,
        'field' => 8,
    ];

    /**
     * @var bool
     */
    public $inbox = true;

    /**
     * Validation closure.
     *
     * @var Closure|null
     */
    protected $validatorSavingCallback;

    /**
     * Whether only render fields.
     *
     * @var bool
     */
    protected $onlyRenderFields = false;

    /**
     * Form constructor.
     *
     * @param array<mixed> $data
     */
    public function __construct($data = [])
    {
        $this->fill($data);

        $this->initFormAttributes();
    }

    /**
     * Get form title.
     *
     * @return mixed
     */
    protected function title()
    {
        return $this->title;
    }

    /**
     * @return array<mixed>
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Fill data to form fields.
     *
     * @param array<mixed>|mixed $data
     *
     * @return $this
     */
    public function fill($data = [])
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        if (!empty($data)) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function sanitize()
    {
        foreach (['_form_', '_token'] as $key) {
            request()->request->remove($key);
        }

        return $this;
    }

    /**
     * Initialize the form attributes.
     *
     * @return void
     */
    protected function initFormAttributes()
    {
        $this->attributes = [
            'method'         => 'POST',
            'action'         => '',
            'class'          => 'form-horizontal ' . $this->getUniqueName(),
            'accept-charset' => 'UTF-8',
            'pjax-container' => true,
            'data-form_uniquename' => $this->getUniqueName(),
        ];
    }


    /**
     * Format form attributes form array to html.
     *
     * @param array<string, mixed> $attributes
     *
     * @return string
     */
    public function formatAttribute($attributes = [])
    {
        $attributes = $attributes ?: $this->attributes;

        if ($this->hasFile()) {
            $attributes['enctype'] = 'multipart/form-data';
        }

        $html = [];
        foreach ($attributes as $key => $val) {
            $html[] = "$key=\"$val\"";
        }

        return implode(' ', $html) ?: '';
    }

    /**
     * Action uri of the form.
     *
     * @param string $action
     *
     * @return $this
     */
    public function action($action)
    {
        return $this->attribute('action', $action);
    }

    /**
     * Method of the form.
     *
     * @param string $method
     *
     * @return $this
     */
    public function method($method = 'POST')
    {
        if (strtolower($method) == 'put') {
            $this->hidden('_method')->default($method);

            return $this;
        }

        return $this->attribute('method', strtoupper($method));
    }

    /**
     * Set submit label.
     *
     * @return $this
     */
    public function submitLabel(string $submitLabel)
    {
        $this->submitLabel = $submitLabel;

        return $this;
    }

    /**
     * Set submit label as save.
     *
     * @return $this
     */
    public function submitLabelSave()
    {
        $this->submitLabel = trans('admin.save');

        return $this;
    }

    /**
     * Disable Pjax.
     *
     * @return $this
     */
    public function disablePjax()
    {
        Arr::forget($this->attributes, 'pjax-container');

        return $this;
    }

    /**
     * Disable reset button.
     *
     * @return $this
     */
    public function disableReset()
    {
        array_delete($this->buttons, 'reset');

        return $this;
    }

    /**
     * Disable submit button.
     *
     * @return $this
     */
    public function disableSubmit()
    {
        array_delete($this->buttons, 'submit');

        return $this;
    }

    /**
     * Set default Checkbox.
     * @param mixed $key
     *
     * @return $this
     */
    public function defaultCheck($key)
    {
        foreach($this->submitRedirects as &$submitRedirect){
            if(Arr::get($submitRedirect, 'key') == $key){
                $submitRedirect['default'] = true;
            }
        }
        
        return $this;
    }

    /**
     * add footer check item.
     *
     * $footerCheck : 
     *     [
     *         'value': 'foo', // this check value name
     *         'label': 'FOO', // this check label
     *         'redirect': \Closure, //set callback. Please redirect.
     *     ]
     * @param array<mixed> $submitRedirect
     * @return $this
     */
    public function submitRedirect(array $submitRedirect)
    {
        $this->submitRedirects[] = $submitRedirect;

        return $this;
    }

    /**
     * Set field and label width in current form.
     *
     * @param int $fieldWidth
     * @param int $labelWidth
     *
     * @return $this
     */
    public function setWidth($fieldWidth = 8, $labelWidth = 2)
    {
        collect($this->fields)->each(function ($field) use ($fieldWidth, $labelWidth) {
            /* @var Field $field  */
            $field->setWidth($fieldWidth, $labelWidth);
        });

        // set this width
        $this->width = [
            'label' => $labelWidth,
            'field' => $fieldWidth,
        ];

        return $this;
    }

    /**
     * Determine if the form has field type.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasField($name)
    {
        return isset(BaseForm::$availableFields[$name]);
    }

    /**
     * Add a form field to form.
     *
     * @param Field $field
     *
     * @return $this
     */
    public function pushField(Field &$field)
    {
        $field->setForm($this);

        array_push($this->fields, $field);

        return $this;
    }

    /**
     * Get all fields of form.
     *
     * @return Field[]
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * @return $this
     */
    public function onlyRenderFields(){
        $this->onlyRenderFields = true;
        return $this;
    }

    /**
     * Get form's script
     *
     * @return array<string>
     */
    public function getScript()
    {
        return collect($this->fields)->map(function ($field) {
            /* @var Field $field  */
            return $field->getScript();
        })->filter()->values()->toArray();
    }

    /**
     * Get variables for render form.
     *
     * @return array<string, mixed>
     */
    protected function getVariables()
    {
        foreach ($this->fields as $field) {
            $field->fill($this->data());
        }

        return [
            'fields'      => $this->fields,
            'attributes'  => $this->formatAttribute(),
            'method'      => $this->attributes['method'],
            'buttons'     => $this->buttons,
            'submitRedirects'=> $this->submitRedirects,
            'width'       => $this->width,
            'submitLabel' => $this->submitLabel ?? static::$defaultSubmitLabel ?? trans('admin.submit'),
            'default_check'    => $this->getDefaultCheck(),
        ];
    }

    /**
     * Determine if form fields has files.
     *
     * @return bool
     */
    public function hasFile()
    {
        foreach ($this->fields as $field) {
            if ($field instanceof Field\File || $field instanceof Field\MultipleFile) {
                return true;
            }
        }

        return false;
    }

    
    /**
     * validatorSavingCallback
     *
     * @param Closure $callback
     * @return $this
     */
    public function validatorSavingCallback(Closure $callback){
        $this->validatorSavingCallback = $callback;

        return $this;
    }
    
    /**
     * Validate this form fields, and return redirect if has errors
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|true
     */
    public function validateRedirect(Request $request)
    {
        $message = $this->validate($request);
        if($message !== false){
            return back()->withInput()->withErrors($message);
        }
        return true;
    }
    

    /**
     * Get default check value
     *
     * @return ?string
     */
    protected function getDefaultCheck(){
        if(!is_null($result = old('after-save'))){
            return $result;
        }
        if(!is_null($result = request()->get('after-save'))){
            return $result;
        }

        foreach ($this->submitRedirects as $submitRedirect) {
            if(boolval(Arr::get($submitRedirect, 'default'))){
                return Arr::get($submitRedirect, 'value');
            }
        }

        return null;
    }
    
    /**
     * Validate this form fields.
     *
     * @param Request $request
     *
     * @return bool|MessageBag
     */
    public function validate(Request $request)
    {
        return $this->validationMessages($request->all());
    }
    

    /**
     * Get validation messages.
     *
     * @param array<mixed> $input
     *
     * @return MessageBag|bool
     */
    public function validationMessages(array $input)
    {
        $message = $this->validationMessageArray($input);

        return $message->any() ? $message : false;
    }

    /**
     * Get validation messages.
     *
     * @param array<mixed> $input
     *
     * @return MessageBag|bool
     */
    public function validationMessageArray(array $input)
    {
        if (method_exists($this, 'form')) {
            $this->form();
        }

        $failedValidators = [];

        /** @var Field $field */
        foreach ($this->fields() as $field) {
            if (!$validator = $field->getValidator($input)) {
                continue;
            }

            if (($validator instanceof Validator) && !$validator->passes()) {
                $failedValidators[] = $validator;
            }
        }

        $message = $this->mergeValidationMessages($failedValidators);

        if($this->validatorSavingCallback){
            $func = $this->validatorSavingCallback;
            $func($input, $message, $this);
        }

        return $message;
    }


    /**
     * Merge validation messages from input validators.
     *
     * @param \Illuminate\Validation\Validator[] $validators
     *
     * @return MessageBag
     */
    protected function mergeValidationMessages($validators)
    {
        $messageBag = new MessageBag();

        foreach ($validators as $validator) {
            $messageBag = $messageBag->merge($validator->messages());
        }

        return $messageBag;
    }

    /**
     * Add a fieldset to form.
     *
     * @param string  $title
     * @param Closure $setCallback
     *
     * @return Field\Fieldset
     */
    public function fieldset(string $title, Closure $setCallback)
    {
        $fieldset = new Field\Fieldset();

        $this->html($fieldset->start($title))->plain();

        $setCallback($this);

        $this->html($fieldset->end())->plain();

        return $fieldset;
    }

    /**
     * @return $this
     */
    public function unbox()
    {
        $this->inbox = false;

        return $this;
    }

    /**
     * @return null
     */
    public function model()
    {
        return null;
    }

    /**
     * @return void
     */
    protected function prepareForm()
    {
        if (method_exists($this, 'form')) {
            $this->form();
        }
    }

    /**
     * @return void
     */
    protected function prepareHandle()
    {
        if (method_exists($this, 'handle')) {
            $this->method('POST');
            $this->action(route('admin.handle-form'));
            $this->hidden('_form_')->default(get_called_class());
        }
    }

    /**
     * Setup scripts.
     *
     * @return void
     */
    protected function setupScript()
    {
        $script = <<<'EOT'
$('.after-submit').iCheck({checkboxClass:'icheckbox_minimal-blue'}).on('ifChecked', function () {
    $('.after-submit').not(this).iCheck('uncheck');
});
EOT;

        \Admin::script($script);
    }

    /**
     * Render the form.
     *
     * @return string
     */
    public function render()
    {
        $this->prepareForm();

        $this->prepareHandle();

        $this->setupScript();

        // if only render fields, set view, and set unique name
        if($this->onlyRenderFields){
            $valiables = $this->getVariables();
            $valiables['uniqueName'] = $this->getUniqueName();
            $form = view('admin::widgets.fields', $valiables);
        }
        else{
            $form = view('admin::widgets.form', $this->getVariables())->render();
        }

        if (!($title = $this->title()) || !$this->inbox) {
            return $form;
        }

        return (new Box($title, $form))->render();
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     *
     * @param string $method
     * @param array<int, mixed>  $arguments
     *
     * @return Field|$this
     */
    public function __call($method, $arguments)
    {
        if (!$this->hasField($method)) {
            return $this;
        }

        $class = BaseForm::$availableFields[$method];

        $field = new $class(Arr::get($arguments, 0), array_slice($arguments, 1));

        return tap($field, function ($field) {
            $this->pushField($field);
        });
    }
}
