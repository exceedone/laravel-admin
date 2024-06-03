<?php

namespace Encore\Admin\Form;

use Encore\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;

class Footer implements Renderable
{
    /**
     * Footer view.
     *
     * @var string
     */
    protected $view = 'admin::form.footer';

    /**
     * Form builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Available buttons.
     *
     * @var array
     */
    protected $buttons = ['reset', 'submit'];

    /**
     * Default Submit label.
     *
     * @var string
     */
    public static $defaultSubmitLabel;

    /**
     * Submit label.
     *
     * @var string
     */
    protected $submitLabel;

    /**
     * Available checkboxes.
     *
     * @var array
     */
    protected $checkboxes = [
        1 => 'continue_editing',
        2 => 'continue_creating',
        3 => 'view',
    ];

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
     *         'default': false, // if this flow is checked, set true
     *     ],
     * ]
     *
     * @var array
     */
    protected $submitRedirects = [];

    /**
     * Footer constructor.
     *
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;

        // set default submitRedirects
        foreach($this->checkboxes as $value => $key){
            $this->enableCheck($key, $value);
        }
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
     * Disable reset button.
     *
     * @return $this
     */
    public function disableReset(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->buttons, 'reset');
        } elseif (!in_array('reset', $this->buttons)) {
            array_push($this->buttons, 'reset');
        }

        return $this;
    }

    /**
     * Disable submit button.
     *
     * @return $this
     */
    public function disableSubmit(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->buttons, 'submit');
        } elseif (!in_array('submit', $this->buttons)) {
            array_push($this->buttons, 'submit');
        }

        return $this;
    }

    /**
     * Disable View Checkbox.
     *
     * @return $this
     */
    public function disableViewCheck(bool $disable = true)
    {
        return $disable ? $this->disableCheck('view') : $this->enableCheck('view', 3);
    }

    /**
     * Disable Editing Checkbox.
     *
     * @return $this
     */
    public function disableEditingCheck(bool $disable = true)
    {
        return $disable ? $this->disableCheck('continue_editing') : $this->enableCheck('continue_editing', 1);
    }

    /**
     * Disable Creating Checkbox.
     *
     * @return $this
     */
    public function disableCreatingCheck(bool $disable = true)
    {
        return $disable ? $this->disableCheck('continue_creating') : $this->enableCheck('continue_creating', 2);
    }

    /**
     * enable Checkbox.
     *
     * @return $this
     */
    protected function enableCheck($key, $value)
    {
        $this->submitRedirects[] = [
            'key' => $key,
            'value' => $value,
            'label' => trans("admin.{$key}"),
        ];

        return $this;
    }
    
    /**
     * Disable Checkbox.
     *
     * @return $this
     */
    protected function disableCheck($key)
    {
        $this->submitRedirects = array_filter($this->submitRedirects, function($submitRedirect) use($key){
            return Arr::get($submitRedirect, 'key') == $key;
        });

        return $this;
    }

    /**
     * Set default Checkbox.
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
     *         'key': 'list', // this check key name. Use default check etc
     *         'value': 'foo', // this check value name
     *         'label': 'FOO', // this check label
     *         'redirect': \Closure, //set callback. Please redirect.
     *     ]
     *
     * @return $this
     */
    public function submitRedirect(array $submitRedirect)
    {
        $this->submitRedirects[] = $submitRedirect;

        return $this;
    }


    /**
     * Get RedirectResponse after data saving.
     *
     * @param string $resourcesPath
     * @param string $key
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string|null
     */
    public function getRedirect($resourcesPath, $key, $afterSaveValue){
        // set submitRedirects
        foreach($this->submitRedirects as $submitRedirect){
            if(Arr::get($submitRedirect, 'value') == $afterSaveValue){
                $url = Arr::get($submitRedirect, 'redirect');
                break;
            }
        }

        if(!isset($url)){
            if ($afterSaveValue == 1) {
                // continue editing
                $url = rtrim($resourcesPath, '/')."/{$key}/edit?after-save=1";
            } elseif ($afterSaveValue == 2) {
                // continue creating
                $url = rtrim($resourcesPath, '/').'/create?after-save=2';
            } elseif ($afterSaveValue == 3) {
                // view resource
                $url = rtrim($resourcesPath, '/')."/{$key}";
            }         
        }

        
        if(!isset($url)){
            return null;
        }
        if(is_string($url)){
            return redirect($url);
        }
        elseif($url instanceof \Closure){
            return $url($resourcesPath, $key);
        }
        return $url;
    }

    /**
     * Setup scripts.
     */
    protected function setupScript()
    {
        $script = <<<'EOT'
$('.after-submit').iCheck({checkboxClass:'icheckbox_minimal-blue'}).on('ifChecked', function () {
    $('.after-submit').not(this).iCheck('uncheck');
});
EOT;

        Admin::script($script);
    }

    /**
     * Render footer.
     *
     * @return string
     */
    public function render()
    {
        $this->setupScript();

        $data = [
            'buttons'      => $this->buttons,
            'checkboxes'   => $this->checkboxes,
            'width'        => $this->builder->getWidth(),
            'submitLabel'  => $this->submitLabel ?? static::$defaultSubmitLabel ?? trans('admin.submit'),
            'submitRedirects'=> $this->submitRedirects,
            'default_check'    => $this->getDefaultCheck(),
        ];

        return view($this->view, $data);
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
}
