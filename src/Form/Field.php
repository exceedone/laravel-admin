<?php

namespace Encore\Admin\Form;

use Closure;
use Encore\Admin\Admin;
use Encore\Admin\Form;
use Encore\Admin\Widgets\Form as WidgetForm;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

/**
 * Class Field.
 */
class Field implements Renderable
{
    use Macroable;

    const FILE_DELETE_FLAG = '_file_del_';
    const FILE_SORT_FLAG = '_file_sort_';

    /**
     * Element id.
     *
     * @var array|string
     */
    protected $id;

    /**
     * Element value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Data of all original columns of value.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Field original value.
     *
     * @var mixed
     */
    protected $original;

    /**
     * Field default value.
     *
     * @var mixed
     */
    protected $default;

    /**
     * Element label.
     *
     * @var string
     */
    protected $label = '';

    /**
     * Whether disable label.
     *
     * @var bool
     */
    protected $disableLabel = false;

    /**
     * Whether disable display.
     *
     * @var bool
     */
    protected $disableDisplayRequired = false;

    /**
     * Column name.
     *
     * @var string|array
     */
    protected $column = '';

    /**
     * HasMany index.
     *
     * @var int
     */
    protected $index;

    /**
     * Form element name.
     */
    protected $elementName = [];

    /**
     * Form element classes.
     *
     * @var array
     */
    protected $elementClass = [];

    /**
     * Variables of elements.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * Options for specify elements.
     */
    protected $options = [];

    /**
     * Checked for specify elements.
     *
     * @var array
     */
    protected $checked = [];

    /**
     * Validation rules.
     *
     * @var string|array|\Closure
     */
    protected $rules = [];

    /**
     * The validation rules for creation.
     *
     * @var array|\Closure
     */
    public $creationRules = [];

    /**
     * The validation rules for updates.
     *
     * @var array|\Closure
     */
    public $updateRules = [];

    /**
     * @var \Closure
     */
    protected $validator;

    /**
     * Validation messages.
     *
     * @var array
     */
    protected $validationMessages = [];

    /**
     * Css required by this field.
     *
     * @var array
     */
    protected static $css = [];

    /**
     * Js required by this field.
     *
     * @var array
     */
    protected static $js = [];

    /**
     * Script for field.
     *
     * @var string
     */
    protected $script = '';

    /**
     * Element attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Parent form.
     *
     * @var Form
     */
    protected $form = null;

    /**
     * View for field to render.
     *
     * @var string
     */
    protected $view = '';

    /**
     * Help Icon.
     */
    protected $helpIcon;

    /**
     * Help Text.
     */
    protected $helpText;

    /**
     * Key for errors.
     *
     * @var mixed
     */
    protected $errorKey;

    /**
     * Placeholder for this field.
     *
     * @var string|array
     */
    protected $placeholder;

    /**
     * Width for label and field.
     *
     * @var array
     */
    protected $width = [
        'label' => 2,
        'field' => 8,
    ];

    /**
     * If the form horizontal layout.
     * If this value is null, get form.
     *
     * @var bool|null
     */
    protected $horizontal = null;

    /**
     * column data format.
     *
     * @var \Closure
     */
    protected $customFormat = null;

    /**
     * @var bool
     */
    protected $display = true;

    /**
     * @var array
     */
    protected $labelClass = [];

    /**
     * @var array
     */
    protected $fieldClass = [];

    /**
     * @var array
     */
    protected $groupClass = [];

    /**
     * @var \Closure
     */
    protected $callback;

    /**
     * @var \Closure
     */
    protected $callbackValue;

    /**
     * @var bool
     */
    public $isJsonType = false;

    /**
     * Whether internal field. If true, Even if not set form's input, set result.
     *
     * @var bool
     */
    protected $internal = false;

    /**
     * @var \Closure
     */
    protected $prepareConfirm;

    /**
     * Field constructor.
     *
     * @param       $column
     * @param array $arguments
     */
    public function __construct($column = '', $arguments = [])
    {
        $this->column = $this->formatColumn($column);
        $this->label = $this->formatLabel($arguments);
        $this->id = $this->formatId($column);
    }

    /**
     * Get assets required by this field.
     *
     * @return array
     */
    public static function getAssets()
    {
        return [
            'css' => static::$css,
            'js'  => static::$js,
        ];
    }

    /**
     * Get form dotted name.
     *
     * @return string
     *
     */
    public static function getDotName($keyname)
    {
        $keyname = str_replace('[]', '.', $keyname);
        $keyname = str_replace('[', '.', $keyname);
        $keyname = str_replace(']', '', $keyname);

        return $keyname;
    }

    /**
     * Format the field column name.
     *
     * @param string $column
     *
     * @return mixed|string
     */
    protected function formatColumn($column = '')
    {
        if (Str::contains($column, '->')) {
            $this->isJsonType = true;

            $column = str_replace('->', '.', $column);
        }

        return $column;
    }

    /**
     * Format the field element id.
     *
     * @param string|array $column
     *
     * @return string|array
     */
    public function formatId($column)
    {
        return str_replace('.', '_', $column);
    }

    /**
     * disable label.
     *
     * @return $this
     */
    public function disableLabel()
    {
        $this->disableLabel = true;

        return $this;
    }

    /**
     * enable label.
     *
     * @return $this
     */
    public function enableLabel()
    {
        $this->disableLabel = false;

        return $this;
    }

    /**
     * Format the label value.
     *
     * @param array $arguments
     *
     * @return string
     */
    protected function formatLabel($arguments = [])
    {
        $column = is_array($this->column) ? current($this->column) : $this->column;

        $label = isset($arguments[0]) ? $arguments[0] : ucfirst($column);

        return str_replace(['.', '_', '->'], ' ', $label);
    }

    /**
     * Format the name of the field.
     *
     * @param string $column
     *
     * @return array|mixed|string
     */
    public function formatName($column)
    {
        if (is_string($column)) {
            if (Str::contains($column, '->')) {
                $name = explode('->', $column);
            } else {
                $name = explode('.', $column);
            }

            if (count($name) == 1) {
                return $name[0];
            }

            $html = array_shift($name);
            foreach ($name as $piece) {
                $html .= "[$piece]";
            }

            return $html;
        }

        if (is_array($this->column)) {
            $names = [];
            foreach ($this->column as $key => $name) {
                $names[$key] = $this->formatName($name);
            }

            return $names;
        }

        return '';
    }

    /**
     * Set form element name.
     *
     * @param string $name
     *
     * @return $this
     *
     * @author Edwin Hui
     */
    public function setElementName($name)
    {
        $this->elementName = $name;

        return $this;
    }

    /**
     * Get form element name.
     *
     * @return string
     *
     */
    public function getElementName()
    {
        return $this->elementName ?: $this->formatName($this->column);
    }

    /**
     * Fill data to the field.
     *
     * @param array $data
     *
     * @return void
     */
    public function fill($data)
    {
        $this->data = $data;

        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                $this->value[$key] = Arr::get($data, $column);
            }

            return;
        }

        $this->value = Arr::get($data, $this->column);

        $this->formatValue();
    }

    /**
     * Format value by passing custom formater.
     */
    protected function formatValue()
    {
        if (isset($this->customFormat) && $this->customFormat instanceof \Closure) {
            $this->value = call_user_func($this->customFormat, $this->value);
        }
    }

    /**
     * custom format form column data when edit.
     *
     * @param \Closure $call
     *
     * @return $this
     */
    public function customFormat(\Closure $call)
    {
        $this->customFormat = $call;

        return $this;
    }

    /**
     * Set original value to the field.
     *
     * @param array $data
     *
     * @return void
     */
    public function setOriginal($data)
    {
        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                $this->original[$key] = Arr::get($data, $column);
            }

            return;
        }

        $this->original = Arr::get($data, $this->column);
    }

    /**
     * @param Form|WidgetForm $form
     *
     * @return $this
     */
    public function setForm($form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Set width for field and label.
     *
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
     * Set the field options.
     *
     * @param array $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Set the field option checked.
     *
     * @param array $checked
     *
     * @return $this
     */
    public function checked($checked = [])
    {
        if ($checked instanceof Arrayable) {
            $checked = $checked->toArray();
        }

        $this->checked = array_merge($this->checked, $checked);

        return $this;
    }

    /**
     * Add `required` attribute to current field if has required rule,
     * except file and image fields.
     *
     * @param array $rules
     */
    protected function addRequiredAttribute($rules)
    {
        if (!is_array($rules)) {
            return;
        }

        if (!in_array('required', $rules)) {
            return;
        }

        if ($this instanceof Form\Field\MultipleFile
            || $this instanceof Form\Field\File
            || get_class($this) == Form\Field\Checkbox::class) {
            return;
        }

        if($this->disableDisplayRequired){
            return;
        }

        $this->required();
    }

    /**
     * If has `required` rule, add required attribute to this field.
     */
    protected function addRequiredAttributeFromRules()
    {
        if (is_null($this->data)) {
            // Create page
            $rules = $this->creationRules ?: $this->rules;
        } else {
            // Update page
            $rules = $this->updateRules ?: $this->rules;
        }

        $this->addRequiredAttribute($rules);
    }

    /**
     * Format validation rules.
     *
     * @param array|string $rules
     *
     * @return array
     */
    protected function formatRules($rules)
    {
        if (is_string($rules)) {
            $rules = array_filter(explode('|', $rules));
        }

        return array_filter((array) $rules);
    }

    /**
     * @param string|array|Closure $input
     * @param string|array         $original
     *
     * @return array|Closure
     */
    protected function mergeRules($input, $original)
    {
        if ($input instanceof Closure) {
            $rules = $input;
        } else {
            if (!empty($original)) {
                $original = $this->formatRules($original);
            }

            $rules = array_merge($original, $this->formatRules($input));
        }

        return $rules;
    }

    /**
     * Set the validation rules for the field.
     *
     * @param array|callable|string $rules
     * @param array                 $messages
     *
     * @return $this
     */
    public function rules($rules = null, $messages = [])
    {
        $this->rules = $this->mergeRules($rules, $this->rules);

        $this->setValidationMessages('default', $messages);

        return $this;
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param array|callable|string $rules
     * @param array                 $messages
     *
     * @return $this
     */
    public function updateRules($rules = null, $messages = [])
    {
        $this->updateRules = $this->mergeRules($rules, $this->updateRules);

        $this->setValidationMessages('update', $messages);

        return $this;
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param array|callable|string $rules
     * @param array                 $messages
     *
     * @return $this
     */
    public function creationRules($rules = null, $messages = [])
    {
        $this->creationRules = $this->mergeRules($rules, $this->creationRules);

        $this->setValidationMessages('creation', $messages);

        return $this;
    }

    /**
     * Remove validation rule
     *
     * @param array|callable|string $rules
     * @return $this
     */
    public function removeRules($rules)
    {
        if(empty($rules)){
            return $this;
        }

        if (is_string($rules)) {
            $rules = array_filter(explode('|', $rules));
        }

        $newRules = [];
        
        foreach($this->rules as $r){
            $isAdd = true;
            foreach($rules as $removeRule){
                if(is_object($r) && $r instanceof $removeRule){
                    $isAdd = false;
                    break;
                }
                elseif(is_string($r)){
                    $rSplit = explode(':', $r);
                    if($removeRule === $rSplit[0]){
                        $isAdd = false;
                        break;
                    }
                }         
            }
            if($isAdd){
                $newRules = $r;
            }
        }
        $this->rules = $newRules;

        return $this;
    }

    /**
     * Set validation messages for column.
     *
     * @param string $key
     * @param array  $messages
     *
     * @return $this
     */
    public function setValidationMessages($key, array $messages)
    {
        $this->validationMessages[$key] = $messages;

        return $this;
    }

    /**
     * Get validation messages for the field.
     *
     * @return array|mixed
     */
    public function getValidationMessages()
    {
        // Default validation message.
        $messages = $this->validationMessages['default'] ?? [];

        if (request()->isMethod('POST')) {
            $messages = $this->validationMessages['creation'] ?? $messages;
        } elseif (request()->isMethod('PUT')) {
            $messages = $this->validationMessages['update'] ?? $messages;
        }

        return $messages;
    }

    /**
     * Get field validation rules.
     */
    protected function getRules()
    {
        if (request()->isMethod('POST')) {
            $rules = $this->creationRules ?: $this->rules;
        } elseif (request()->isMethod('PUT')) {
            $rules = $this->updateRules ?: $this->rules;
        } else {
            $rules = $this->rules;
        }

        if ($rules instanceof \Closure) {
            $rules = $rules->call($this, $this->form);
        }

        if (is_string($rules)) {
            $rules = array_filter(explode('|', $rules));
        }

        if (!$this->form || !$this->form->model()) {
            return $rules;
        }

        if (!$id = $this->form->model()->getKey()) {
            return $rules;
        }

        foreach ($rules as &$rule) {
            if (is_string($rule)) {
                $rule = str_replace('{{id}}', $id, $rule);
            }
        }

        return $rules;
    }

    /**
     * Remove a specific rule by keyword.
     *
     * @param string $rule
     *
     * @return void
     */
    public function removeRule($rule)
    {
        if (is_array($this->rules)) {
            array_delete($this->rules, $rule);

            return;
        }

        if (!is_string($this->rules)) {
            return;
        }

        $pattern = "/{$rule}[^\|]?(\||$)/";
        $this->rules = preg_replace($pattern, '', $this->rules, -1);
    }

    /**
     * Set field validator.
     *
     * @param callable $validator
     *
     * @return $this
     */
    public function validator(callable $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Get key for error message.
     *
     * @return string
     */
    public function getErrorKey()
    {
        return $this->errorKey ?: $this->column;
    }

    /**
     * Set key for error message.
     *
     * @param string $key
     *
     * @return $this
     */
    public function setErrorKey($key)
    {
        $this->errorKey = $key;

        return $this;
    }

    /**
     * Set or get value of the field.
     *
     * @param null $value
     *
     * @return mixed
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            return is_null($this->value) ? $this->getDefault() : $this->value;
        }

        $this->value = $value;

        return $this;
    }

    /**
     * Set or get data.
     *
     * @param array $data
     *
     * @return $this
     */
    public function data(array $data = null)
    {
        if (is_null($data)) {
            return $this->data;
        }

        $this->data = $data;

        return $this;
    }

    /**
     * Set default value for field.
     *
     * @param $default
     *
     * @return $this
     */
    public function default($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Get default value.
     *
     * @return mixed
     */
    public function getDefault()
    {
        if ($this->default instanceof \Closure) {
            return call_user_func($this->default, $this->form);
        }

        return $this->default;
    }

    /**
     * Set help block for current field.
     *
     * @param string $text
     * @param string $icon
     *
     * @return $this
     */
    public function help($text = '', $icon = 'fa-info-circle')
    {
        $this->helpText = $text;
        $this->helpIcon = $icon;

        return $this;
    }

    /**
     * append help text.
     *
     * @param string $text
     *
     * @return $this
     */
    public function appendHelp($text = '')
    {
        $this->helpText .= $text;
        if(empty($this->helpIcon)){
            $this->helpIcon = 'fa-info-circle';
        }

        return $this;
    }

    /**
     * Get help Text
     *
     * @return string
     */
    public function getHelpText()
    {
        return $this->helpText;
    }

    /**
     * forget help
     *
     * @return $this
     */
    public function forgetHelp()
    {
        $this->helpIcon = null;
        $this->helpText = null;

        return $this;
    }

    /**
     * forget help
     *
     * @return array
     */
    protected function getHelpArray() : array
    {
        $help = [];
        if(isset($this->helpIcon)){
            $help['icon'] = $this->helpIcon;
        }
        if(isset($this->helpText)){
            if(function_exists('html_clean')){
                $help['text'] = html_clean($this->helpText);
            }else{
                $help['text'] = $this->helpText;
            }
        }
        return $help;
    }

    /**
     * Get column of the field.
     *
     * @return string|array
     */
    public function column()
    {
        return $this->column;
    }

    /**
     * Get label of the field.
     *
     * @return string
     */
    public function label()
    {
        return $this->label;
    }

    /**
     * Get original value of the field.
     *
     * @return mixed
     */
    public function original()
    {
        return $this->original;
    }

    /**
     * Get validator for this field.
     *
     * @param array $input
     *
     * @return bool|\Illuminate\Contracts\Validation\Validator|mixed
     */
    public function getValidator(array $input)
    {
        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        $rules = $attributes = [];

        if (!$fieldRules = $this->getRules()) {
            return false;
        }

        if (is_string($this->column)) {
            if (!Arr::has($input, $this->column)) {
                return false;
            }

            $input = $this->sanitizeInput($input, $this->column);

            $rules[$this->column] = $fieldRules;
            $attributes[$this->column] = $this->label;
        }

        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                if (!array_key_exists($column, $input)) {
                    continue;
                }
                $input[$column.$key] = Arr::get($input, $column);
                $rules[$column.$key] = $fieldRules;
                $attributes[$column.$key] = $this->label."[$column]";
            }
        }

        return \validator($input, $rules, $this->getValidationMessages(), $attributes);
    }

    /**
     * Sanitize input data.
     *
     * @param array  $input
     * @param string $column
     *
     * @return array
     */
    protected function sanitizeInput($input, $column)
    {
        if ($this instanceof Field\MultipleSelect) {
            $value = Arr::get($input, $column);
            if(is_null($value)){
                $value = [];
            }
            
            if(is_string($value) || is_int($value)){
                $value = explode(',', $value);
            }

            if($value instanceof \Illuminate\Support\Collection){
                $value = $value->toArray();
            }

            Arr::set($input, $column, array_filter($value));
        }

        return $input;
    }

    /**
     * Add html attributes to elements.
     *
     * @param array|string $attribute
     * @param mixed        $value
     *
     * @return $this
     */
    public function attribute($attribute, $value = null)
    {
        if (is_array($attribute)) {
            $this->attributes = array_merge($this->attributes, $attribute);
        } else {
            $this->attributes[$attribute] = (string) $value;
        }

        return $this;
    }

    /**
     * Get field attributes.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Specifies a regular expression against which to validate the value of the input.
     *
     * @param string $regexp
     */
    public function pattern($regexp)
    {
        return $this->attribute('pattern', $regexp);
    }

    /**
     * set the input filed required.
     *
     * @param bool $isLabelAsterisked
     *
     * @return Field
     */
    public function required($isLabelAsterisked = true)
    {
        if ($isLabelAsterisked) {
            $this->setLabelClass(['asterisk']);
        }

        //ToDo: if supported dynamic required
        //$this->rules('required');

        return $this->attribute('required', true);
    }

    /**
     * set the input filed required rule.
     * Set asterisk, set validation browser, and set rule
     *
     * @return $this
     */
    public function requiredRule()
    {
        $this->required();
        $this->rules('required');

        return $this;
    }

    /**
     * Set the field automatically get focus.
     *
     * @return Field
     */
    public function autofocus()
    {
        return $this->attribute('autofocus', true);
    }

    /**
     * Set the field as readonly mode.
     *
     * @return Field
     */
    public function setReadonly($readonly)
    {
        if($readonly){
            return $this->attribute('readonly', true);
        }else{
            Arr::forget($this->attributes, 'readonly');
        }

        return $this;
    }

    /**
     * Set the field as readonly mode.
     *
     * @return Field
     */
    public function readonly()
    {
        return $this->setReadonly(true);
    }

    /**
     * Set field as disabled.
     *
     * @return Field
     */
    public function disable($disable = true)
    {
        if($disable){
            $this->attribute('disabled', true);
        }

        return $this;
    }

    /**
     * Set field placeholder.
     *
     * @param string $placeholder
     *
     * @return Field
     */
    public function placeholder($placeholder = '')
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Get placeholder.
     *
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
        //return $this->placeholder ?: trans('admin.input').' '.$this->label;
    }

    /**
     * Get old function result. Contains value.
     */
    public function getOld()
    {
        $value = $this->value();

        if(is_array($this->column)){    
            $olds = [];
            foreach($this->column as $key => $c){
                $elementNames = $this->getElementName();
                $elementName = is_array($elementNames) ? Arr::get($elementNames, $key) : $elementNames;

                $keyname = static::getDotName($elementName);
                $v = Arr::get((is_null($value) ? [] : (array)$value), $key);

                if(!is_null($old = old($c, $v))){
                    $olds[$key] = $old;
                }
        
                // replace element name
                $olds[$key] = old("$keyname.$key", $v);
            }

            return $olds;
        }else{
            $keyname = static::getDotName($this->getElementName());
    
            return old($keyname, $value);

            // $old = old($this->column, $value);
            // if(!is_null($old) && !empty($old)){
            //     return $old;
            // }
            // return old($keyname, $value);
        }
    }

    /**
     * Prepare for a field value before update or insert.
     *
     * @param $value
     *
     * @return mixed
     */
    public function prepare($value)
    {
        return $value;
    }

    /**
     * Prepare for confirm(preview). Almost is same prepare($value), but file is not saving
     *
     * @param $value
     *
     * @return mixed
     */
    public function prepareConfirm($value)
    {
        if(!$this->prepareConfirm){
            return $this->prepare($value);
        }
        return call_user_func($this->prepareConfirm, $value);
    }

    /**
     * Set Prepare for confirm(preview). Almost is same prepare($value), but file is not saving
     *
     * @param $value
     *
     * @return mixed
     */
    public function setPrepareConfirm(\Closure $callback)
    {
        $this->prepareConfirm = $callback;
        return $this;
    }

    /**
     * Format the field attributes.
     *
     * @return string
     */
    protected function formatAttributes()
    {
        $html = [];

        foreach ($this->attributes as $name => $value) {
            $html[] = $name.'="'.e($value).'"';
        }

        return implode(' ', $html);
    }

    /**
     * @return bool
     */
    public function getHorizontal()
    {
        if(is_null($this->horizontal)){
            if($this->form){
                return $this->form->getHorizontal();
            }
            return true;
        }
        return $this->horizontal;
    }

    /**
     * @return $this
     */
    public function setHorizontal(bool $horizontal)
    {
        $this->horizontal = $horizontal;
        
        return $this; 
    }

    /**
     * @return $this
     */
    public function disableHorizontal()
    {
        $this->horizontal = false;

        return $this;
    }

    /**
     * @return array
     */
    public function getViewElementClasses()
    {
        if ($this->getHorizontal()) {
            return [
                'label'      => "col-md-{$this->width['label']} {$this->getLabelClass()}",
                'field'      => "col-md-{$this->width['field']} {$this->getFieldClass()}",
                'form-group' => $this->getGroupClass(true),
            ];
        }

        return ['label' => "{$this->getLabelClass()}", 'field' => "{$this->getFieldClass()}", 'form-group' => 'form-group-vertical'];
    }

    /**
     * Set form element class.
     *
     * @param string|array $class
     *
     * @return $this
     */
    public function setElementClass($class)
    {
        $this->elementClass = array_merge($this->elementClass, (array) $class);

        $this->elementClass = array_unique($this->elementClass);

        return $this;
    }

    /**
     * Get element class.
     *
     * @return array
     */
    protected function getElementClass()
    {
        if (!$this->elementClass) {
            $name = $this->elementName ?: $this->formatName($this->column);

            $this->elementClass = (array) str_replace(['[', ']'], '_', $name);
        }

        return $this->elementClass;
    }

    /**
     * Get element class string.
     *
     * @return mixed
     */
    protected function getElementClassString()
    {
        $elementClass = $this->getElementClass();

        if (Arr::isAssoc($elementClass)) {
            $classes = [];

            foreach ($elementClass as $index => $class) {
                $classes[$index] = is_array($class) ? implode(' ', $class) : $class;
            }

            return $classes;
        }

        return implode(' ', $elementClass);
    }

    /**
     * Get element class selector.
     *
     * @param boolean $appendFormName if true, set form unique name
     * @return string|array
     */
    protected function getElementClassSelector($appendFormName = true)
    {
        $elementClass = $this->getElementClass();

        if (Arr::isAssoc($elementClass)) {
            $classes = [];

            foreach ($elementClass as $index => $class) {
                $classes[$index] = '.'.(is_array($class) ? implode('.', $class) : $class);
            }

            return $classes;
        }

        // Append form class name for filtering class name in form
        $class = '';
        if(boolval($appendFormName) && !is_null($this->getFormUniqueName())){
            $class .= '.' . $this->getFormUniqueName() . ' ';
        }
        return $class . '.'.implode('.', $elementClass);
    }

    /**
     * Add the element class.
     *
     * @param $class
     *
     * @return $this
     */
    public function addElementClass($class)
    {
        if (is_array($class) || is_string($class)) {
            $this->elementClass = array_merge($this->elementClass, (array) $class);

            $this->elementClass = array_unique($this->elementClass);
        }

        return $this;
    }

    /**
     * Remove element class.
     *
     * @param $class
     *
     * @return $this
     */
    public function removeElementClass($class)
    {
        $delClass = [];

        if (is_string($class) || is_array($class)) {
            $delClass = (array) $class;
        }

        foreach ($delClass as $del) {
            if (($key = array_search($del, $this->elementClass)) !== false) {
                unset($this->elementClass[$key]);
            }
        }

        return $this;
    }

    /**
     * Set form group class.
     *
     * @param string|array $class
     *
     * @return $this
     */
    public function setGroupClass($class)
    : self
    {
        if (is_array($class)) {
            $this->groupClass = array_merge($this->groupClass, $class);
        } else {
            array_push($this->groupClass, $class);
        }

        return $this;
    }

    /**
     * Get element class.
     *
     * @param bool $default
     *
     * @return string
     */
    protected function getGroupClass($default = false)
    : string
    {
        return ($default ? 'form-group ' : '').implode(' ', array_filter($this->groupClass));
    }

    /**
     * reset field className.
     *
     * @param string $className
     * @param string $resetClassName
     *
     * @return $this
     */
    public function resetElementClassName(string $className, string $resetClassName)
    {
        if (($key = array_search($className, $this->getElementClass())) !== false) {
            $this->elementClass[$key] = $resetClassName;
        }

        return $this;
    }

    /**
     * Add variables to field view.
     *
     * @param array $variables
     *
     * @return $this
     */
    protected function addVariables(array $variables = [])
    {
        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelClass()
    : string
    {
        if($this->disableLabel && in_array('asterisk', $this->labelClass)){
            $this->labelClass = array_diff($this->labelClass, ['asterisk']);
            $this->labelClass = array_values($this->labelClass);
        }
        return implode(' ', $this->labelClass);
    }

    /**
     * @param array $labelClass
     *
     * @return self
     */
    public function setLabelClass(array $labelClass)
    : self
    {
        $this->labelClass = $labelClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldClass()
    : string
    {
        return implode(' ', $this->fieldClass);
    }

    /**
     * @param array $fieldClass
     *
     * @return self
     */
    public function setFieldClass($fieldClass)
    : self
    {
        $this->fieldClass = array_merge($this->fieldClass, (array) $fieldClass);

        $this->fieldClass = array_unique($this->fieldClass);

        return $this;
    }

    /**
     * Get form unique class name for class selector
     *
     * @return  string
     */ 
    public function getFormUniqueName()
    {
        return $this->form ? $this->form->getUniqueName() : null;
    }

    /**
     * @return int
     */
    public function getIndex()
    : ?int
    {
        return $this->index;
    }

    /**
     * @param int $index
     *
     * @return self
     */
    public function setIndex(?int $index)
    : self
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Get whether internal field. If true, Even if not set form's input, set result.
     *
     * @return  bool
     */ 
    public function getInternal()
    {
        return $this->internal;
    }

    /**
     * Set whether internal field. If true, Even if not set form's input, set result.
     *
     * @param  bool  $internal  Whether internal field. If true, Even if not set form's input, set result.
     *
     * @return  self
     */ 
    public function setInternal(bool $internal)
    {
        $this->internal = $internal;

        return $this;
    }
    
    /**
     * Get the view variables of this field.
     *
     * @return array
     */
    public function variables()
    {
        return array_merge($this->variables, [
            'id'          => $this->id,
            'name'        => $this->getElementName(),
            'help'        => $this->getHelpArray(),
            'class'       => $this->getElementClassString(),
            'value'       => $this->value(),
            'label'       => $this->disableLabel ? '' : $this->label,
            'viewClass'   => $this->getViewElementClasses(),
            'column'      => $this->column,
            'errorKey'    => $this->getErrorKey(),
            'attributes'  => $this->formatAttributes(),
            'placeholder' => $this->getPlaceholder(),
            'old' => $this->getOld(),
        ]);
    }

    /**
     * Get view of this field.
     *
     * @return string
     */
    public function getView()
    {
        if (!empty($this->view)) {
            return $this->view;
        }

        $class = explode('\\', get_called_class());

        return 'admin::form.'.strtolower(end($class));
    }

    /**
     * Set view of current field.
     *
     * @param string $view
     *
     * @return string
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get script of current field.
     *
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Set script of current field.
     *
     * @param string $script
     *
     * @return $this
     */
    public function setScript($script)
    {
        $this->script = $script;

        return $this;
    }

    /**
     * To set this field should render or not.
     *
     * @param bool $display
     *
     * @return $this
     */
    public function setDisplay(bool $display)
    {
        $this->display = $display;

        return $this;
    }

    /**
     * If this field should render.
     *
     * @return bool
     */
    protected function shouldRender()
    {
        if (!$this->display) {
            return false;
        }

        return true;
    }

    /**
     * @param \Closure $callback
     *
     * @return \Encore\Admin\Form\Field
     */
    public function with(Closure $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @param \Closure $callback
     *
     * @return \Encore\Admin\Form\Field
     */
    public function callbackValue(Closure $callback)
    {
        $this->callbackValue = $callback;

        return $this;
    }

    /**
     * Render this filed.
     */
    public function render()
    {
        if (!$this->shouldRender()) {
            return '';
        }

        if ($this->callbackValue instanceof Closure) {
            $this->value = $this->callbackValue->call($this, $this->value);
        }

        if ($this->callback instanceof Closure) {
            $this->value = $this->callback->call($this->form->model(), $this->value, $this);
        }

        $this->addRequiredAttributeFromRules();

        Admin::script($this->script);

        return view($this->getView(), $this->variables());
    }

    public function __toString()
    {
        return $this->render()->render();
    }
}
