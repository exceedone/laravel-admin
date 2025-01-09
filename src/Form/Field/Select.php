<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Validator\HasOptionRule;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form\Field;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Select extends Field
{
    /**
     * @var array<string>
     */
    protected static $css = [
        '/vendor/open-admin/AdminLTE/plugins/select2/select2.min.css',
    ];

    /**
     * @var array<string>
     */
    protected static $js = [
        '/vendor/open-admin/AdminLTE/plugins/select2/select2.full.min.js',
    ];

    /**
     * @var string 
     */
    public static $modalSelectorName = '#modal-showmodal';

    /**
     * @var array<mixed>
     */
    protected $groups = [];

    /**
     * @var array<mixed>
     */
    protected $buttons = [];

    /**
     * @var bool
     */
    protected $escapeMarkup;

    /**
     * @var array<string>
     */
    protected $config = [];

    /**
     * @var boolean
     */
    protected $freeInput = false;

    /**
     * @var boolean
     */
    protected $addEmpty = true;

    /**
     * @var mixed options for validation.
     */
    protected $validationOptions;

    /**
     * @var bool Whether is select is modal.
     * https://select2.org/troubleshooting/common-problems
     */
    protected $asModal = false;

    /**
     * Field constructor.
     *
     * @param string $column
     * @param array<mixed> $arguments
     */
    public function __construct($column = '', $arguments = [])
    {
        parent::__construct($column, $arguments);

        $this->rules([new HasOptionRule($this)]);
    }

    /**
     * Get options.
     * *Not set $this->options*
     * @param mixed|null $value
     *
     * @return array<mixed>
     */
    public function getOptions($value = null) : array
    {
        if($this->validationOptions){
            $options = $this->validationOptions;
        }
        else{
            $options = $this->options;
        }

        if ($options instanceof \Closure) {
            $options = call_user_func($options, ($value ?? $this->value), $this, isset($this->form) ? $this->form->model() : null);
        }

        if($options instanceof \Illuminate\Support\Collection){
            $options = $options->toArray();
        }

        if(is_null($options)){
            return [];
        }

        /** @phpstan-ignore-next-line Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, 'strlen' given. */
        return array_filter($options, 'strlen');
    }

    /**
     * Set options.
     *
     * @param array<mixed>|callable|string $options
     *
     * @return $this|mixed
     */
    public function options($options = [])
    {
        // remote options
        if (is_string($options)) {
            // reload selected
            if (class_exists($options) && in_array(Model::class, class_parents($options))) {
                return $this->model(...func_get_args());
            }

            return $this->loadRemoteOptions(...func_get_args());
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        if (is_callable($options)) {
            $this->options = $options;
        } else {
            $this->options = (array) $options;
        }

        return $this;
    }

    /**
     * Set options for validation.
     *
     * @param array<mixed>|callable|string $options
     *
     * @return $this
     */
    public function validationOptions($options)
    {
        $this->validationOptions = $options;

        return $this;
    }

    /**
     * Set the markup. render as html
     *
     * @param  bool  $escapeMarkup
     *
     * @return  self
     */ 
    public function escapeMarkup(bool $escapeMarkup)
    {
        $this->escapeMarkup = $escapeMarkup;

        return $this;
    }

    /**
     * @param array $groups
     */

    /**
     * Set option groups.
     *
     * eg: $group = [
     *        [
     *        'label' => 'xxxx',
     *        'options' => [
     *            1 => 'foo',
     *            2 => 'bar',
     *            ...
     *        ],
     *        ...
     *     ]
     *
     * @param array<mixed> $groups
     *
     * @return $this
     */
    public function groups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }


    /**
     * Set option buttons.
     *
     * eg: $buttons = [
     *        [
     *        'label' => 'xxxx',
     *        'btn_class' => 'xxxx',
     *        'icon' => 'xxxx',
     *        'attributes' => [
     *           'xxx' => 'yyyy',
     *         ],
     *            ...
     *        ],
     *        ...
     *     ]

     * @param array<mixed> $buttons
     * @return $this
     */
    public function buttons(array $buttons)
    {
        $this->buttons = collect($buttons)->map(function($button){
            $attributes = Arr::get($button, 'attributes', []);
            $html = [];
            
            foreach ($attributes as $name => $value) {
                $html[] = $name.'="'.e($value).'"';
            }
            $button['attribute'] = implode(' ', $html);
            return $button;
        })->toArray();

        return $this;
    }

    /**
     * Set as Modal.
     *
     * https://select2.org/troubleshooting/common-problems
     *
     * @return $this
     */
    public function asModal()
    {
        $this->asModal = true;

        return $this;
    }
    
    /**
     * Set free input option
     * 
     * @param boolean $freeInput
     *
     * @return $this
     */
    public function freeInput(bool $freeInput)
    {
        $this->freeInput = $freeInput;

        return $this;
    }
    
    /**
     * Set add empty option
     * 
     * @param boolean $addEmpty
     *
     * @return $this
     */
    public function addEmpty(bool $addEmpty)
    {
        $this->addEmpty = $addEmpty;

        return $this;
    }

    /**
     * Load options for other select on change.
     *
     * @param string $field
     * @param string $sourceUrl
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function load($field, $sourceUrl, $idField = 'id', $textField = 'text', bool $allowClear = true)
    {
        if (Str::contains($field, '.')) {
            $field = $this->formatName($field);
            $class = str_replace(['[', ']'], '_', $field);
        } else {
            $class = $field;
        }

        $placeholder = json_encode([
            'id'   => '',
            'text' => trans('admin.choose'),
        ]);

        $freeInput = $this->freeInput ? '1' : '0';

        $script = <<<EOT
$(document).off('change', "{$this->getElementClassSelector()}");
$(document).on('change', "{$this->getElementClassSelector()}", function () {
    var target = $(this).closest('.fields-group').find(".$class");
    $.get("$sourceUrl",{q : this.value}, function (data) {
        target.find("option").remove();
        $(target).select2({
            placeholder: $placeholder,
            allowClear: $allowClear,
            tags: $freeInput,
            data: $.map(data, function (d) {
                d.id = d.$idField;
                d.text = d.$textField;
                return d;
            })
        }).trigger('change');
    });
});
EOT;

        Admin::script($script);

        return $this;
    }

    /**
     * Load options for other selects on change.
     *
     * @param array<mixed>  $fields
     * @param array<mixed>  $sourceUrls
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function loads($fields = [], $sourceUrls = [], $idField = 'id', $textField = 'text', bool $allowClear = true)
    {
        $fieldsStr = implode('.', $fields);
        $urlsStr = implode('^', $sourceUrls);

        $placeholder = json_encode([
            'id'   => '',
            'text' => trans('admin.choose'),
        ]);

        $freeInput = $this->freeInput ? '1' : '0';

        $script = <<<EOT
var fields = '$fieldsStr'.split('.');
var urls = '$urlsStr'.split('^');

var refreshOptions = function(url, target) {
    $.get(url).then(function(data) {
        target.find("option").remove();
        $(target).select2({
            placeholder: $placeholder,
            allowClear: $allowClear,        
            tags: $freeInput,
            data: $.map(data, function (d) {
                d.id = d.$idField;
                d.text = d.$textField;
                return d;
            })
        }).trigger('change');
    });
};

$(document).off('change', "{$this->getElementClassSelector()}");
$(document).on('change', "{$this->getElementClassSelector()}", function () {
    var _this = this;
    var promises = [];

    fields.forEach(function(field, index){
        var target = $(_this).closest('.fields-group').find('.' + fields[index]);
        promises.push(refreshOptions(urls[index] + "?q="+ _this.value, target));
    });
});
EOT;

        Admin::script($script);

        return $this;
    }

    /**
     * Load options from current selected resource(s).
     *
     * @param string $model
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function model($model, $idField = 'id', $textField = 'name')
    {
        if (
            !class_exists($model)
            || !in_array(Model::class, class_parents($model))
        ) {
            throw new \InvalidArgumentException("[$model] must be a valid model class");
        }

        $this->options = function ($value) use ($model, $idField, $textField) {
            if (empty($value)) {
                return [];
            }

            $resources = [];

            if (is_array($value)) {
                if (Arr::isAssoc($value)) {
                    $resources[] = Arr::get($value, $idField);
                } else {
                    $resources = array_column($value, $idField);
                }
            } else {
                $resources[] = $value;
            }

            return $model::find($resources)->pluck($textField, $idField)->toArray();
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function disableClear(){
        return $this->config('allowClear', false);
    }

    /**
     * Load options from remote.
     *
     * @param string $url
     * @param array<mixed>  $parameters
     * @param array<mixed>  $options
     *
     * @return $this
     */
    protected function loadRemoteOptions($url, $parameters = [], $options = [])
    {
        $ajaxOptions = [
            'url' => $url.'?'.http_build_query($parameters),
        ];
        $configs = array_merge([
            'allowClear'         => true,
            'tags'               => $this->freeInput,
            'placeholder'        => [
                'id'        => '',
                'text'      => trans('admin.choose'),
            ],
        ], $this->config);

        $configs = json_encode($configs);
        $configs = substr($configs, 1, strlen($configs) - 2);

        $ajaxOptions = json_encode(array_merge($ajaxOptions, $options));

        $this->script = <<<EOT

$.ajax($ajaxOptions).done(function(data) {

  var select = $("{$this->getElementClassSelector()}");

  select.select2({
    data: data,
    $configs
  });
  
  var value = select.data('value') + '';
  
  if (value) {
    value = value.split(',');
    select.select2('val', value);
  }
});

EOT;

        return $this;
    }

    /**
     * Load options from ajax results.
     *
     * @param string $url
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function ajax($url, $idField = 'id', $textField = 'text')
    {
        if(empty($url)){
            return $this;
        }

        $configs = array_merge([
            'allowClear'         => true,
            'placeholder'        => $this->label,
            'minimumInputLength' => 1,
            'tags'               => $this->freeInput,
        ], $this->config);

        $configs = json_encode($configs);
        $configs = substr($configs, 1, strlen($configs) - 2);
        $dropdownParent = $this->asModal ? '$("' . static::$modalSelectorName . ' .modal-dialog")' : 'null';

        $this->script = <<<EOT

$("{$this->getElementClassSelector()}").not('.admin-added-select2').select2({
  ajax: {
    url: "$url",
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term,
        page: params.page,
      };
    },
    processResults: function (data, params) {
      params.page = params.page || 1;

      return {
        results: $.map(data.data, function (d) {
                   d.id = d.$idField;
                   d.text = d.$textField;
                   return d;
                }),
        pagination: {
          more: data.next_page_url
        }
      };
    },
    cache: true
  },
  $configs,
  dropdownParent: $dropdownParent,
  escapeMarkup: function (markup) {
      return markup;
  }
}).addClass('admin-added-select2');

EOT;

        return $this;
    }

    /**
     * Set config for select2.
     *
     * all configurations see https://select2.org/configuration/options-api
     *
     * @param string $key
     * @param mixed  $val
     *
     * @return $this
     */
    public function config($key, $val)
    {
        $this->config[$key] = $val;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function readonly()
    {
        $this->config('containerCssClass', 'select2-readonly');

        return parent::readonly();
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function render()
    {
        $configs = array_merge([
            'allowClear'  => true,
            'tags'        => $this->freeInput,
            'language' =>  \App::getLocale(),
            'placeholder' => [
                'id'   => '',
                'text' => $this->getPlaceholder(),
            ],
        ], $this->config);

        $configs = json_encode($configs);
        $configs = substr($configs, 1, strlen($configs) - 2);

        if (empty($this->script)) {
            $dropdownParent = $this->asModal ? '$("' . static::$modalSelectorName . ' .modal-dialog")' : 'null';
            $this->script = "$(\"{$this->getElementClassSelector()}\").not('.admin-added-select2').select2({
                dropdownParent: $dropdownParent,
                $configs,
            }).addClass('admin-added-select2');";
        }

        if($this->escapeMarkup){
            $this->script .= "$(\"{$this->getElementClassSelector()}\").select2({
                escapeMarkup: function(markup) {
                    return markup;
                }
            });";
        }

        if ($this->options instanceof \Closure) {
            if ($this->form && $this->form->model()) {
                $this->options = $this->options->bindTo($this->form->model());
            }

            $this->options(call_user_func($this->options, $this->value, $this, isset($this->form) ? $this->form->model() : null));
        }

        /** @phpstan-ignore-next-line Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, 'strlen' given. */
        $this->options = array_filter($this->options, 'strlen');

        $this->addVariables([
            'options' => $this->options,
            'groups'  => $this->groups,
            'buttons'  => $this->buttons,
            'addEmpty'  => $this->addEmpty,
        ]);

        $this->attribute('data-value', implode(',', (array) $this->value()));

        return parent::render();
    }

    /**
     * {@inheritdoc}
     * @return array<string, array<string>>
     */
    public static function getAssets()
    {
        $assets = [
            'css' => static::$css,
            'js'  => static::$js,
        ];

        // add select2 lang file
        if(in_array(get_called_class(), [Select::class, MultipleSelect::class])){
            $assets['js'][] = '/vendor/open-admin/AdminLTE/plugins/select2/i18n/' . \App::getLocale() . '.js';
        }

        return $assets;
    }

}
