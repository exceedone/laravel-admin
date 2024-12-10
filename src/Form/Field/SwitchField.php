<?php

namespace OpenAdmin\Admin\Form\Field;

use OpenAdmin\Admin\Form\Field;
use OpenAdmin\Admin\Validator\HasOptionRule;
use Illuminate\Support\Arr;

class SwitchField extends Field
{
    /**
     * @var array<string>
     */
    protected static $css = [
        '/vendor/open-admin/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css',
    ];

    /**
     * @var array<string>
     */
    protected static $js = [
        '/vendor/open-admin/bootstrap-switch/dist/js/bootstrap-switch.min.js',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $states = [
        'on'  => ['value' => 1, 'text' => 'ON', 'color' => 'primary'],
        'off' => ['value' => 0, 'text' => 'OFF', 'color' => 'default'],
    ];

    /**
     * @var string
     */
    protected $size = 'small';

    /**
     * Field constructor.
     *
     * @param string     $column
     * @param array<mixed> $arguments
     */
    public function __construct($column = '', $arguments = [])
    {
        parent::__construct($column, $arguments);

        $this->rules([new HasOptionRule($this)]);
    }

    /**
     * @return array<mixed>
     */
    public function getOptions(){
        return collect($this->states)->mapWithKeys(function($state){
            return [Arr::get($state, 'value') => Arr::get($state, 'text')];
        })->toArray();
    }

    /**
     * @param string $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @param array<mixed> $states
     * @return $this
     */
    public function states($states = [])
    {
        foreach (Arr::dot($states) as $key => $state) {
            Arr::set($this->states, $key, $state);
        }

        return $this;
    }

    /**
     * @param string $value
     * @return string
     */
    public function prepare($value)
    {
        if (isset($this->states[$value])) {
            return $this->states[$value]['value'];
        }

        return $value;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|string
     */
    public function render()
    {
        if(is_null($this->value())){
            $this->value = $this->states['off']['value'];
        }else{
            foreach ($this->states as $state => $option) {
                if ($this->value() == $option['value']) {
                    $this->value = $state;
                    break;
                }
            }
        }

        $this->script = <<<EOT

$('{$this->getElementClassSelector()}.la_checkbox').bootstrapSwitch({
    size:'{$this->size}',
    onText: '{$this->states['on']['text']}',
    offText: '{$this->states['off']['text']}',
    onColor: '{$this->states['on']['color']}',
    offColor: '{$this->states['off']['color']}',
    onSwitchChange: function(event, state) {
        $(event.target).closest('.bootstrap-switch').next().val(state ? 'on' : 'off').change();
    }
});

EOT;

        return parent::render();
    }
}
