<?php

namespace OpenAdmin\Admin\Form\Field;

class Date extends Text
{
    /**
     * @var array<string>
     */
    protected static $css = [
        '/vendor/open-admin/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
    ];

    /**
     * @var array<string>
     */
    protected static $js = [
        '/vendor/open-admin/moment/min/moment-with-locales.min.js',
        '/vendor/open-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
    ];

    /**
     * @var string
     */
    protected $format = 'YYYY-MM-DD';

    /**
     * @param string $format
     * @return $this
     */
    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param string $value
     * @return mixed|null
     */
    public function prepare($value)
    {
        if ($value === '') {
            $value = null;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->options['format'] = $this->format;
        $this->options['locale'] = config('app.locale');
        $this->options['allowInputToggle'] = true;

        $this->script = "$('{$this->getElementClassSelector()}').parent().datetimepicker(".json_encode($this->options).');';

        $this->prepend('<i class="fa fa-calendar fa-fw"></i>')
            ->defaultAttribute('style', 'width: 110px')
            ->defaultAttribute('autocomplete', 'off');

        return parent::render();
    }
}
