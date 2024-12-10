<?php

namespace OpenAdmin\Admin\Form\Field;

/**
 * Class ListBox.
 *
 * @see https://github.com/istvan-ujjmeszaros/bootstrap-duallistbox
 */
class Listbox extends MultipleSelect
{
    /**
     * @var array<mixed>
     */
    protected $settings = [];

    /**
     * @var array<string>
     */
    protected static $css = [
        '/vendor/open-admin/bootstrap-duallistbox/dist/bootstrap-duallistbox.min.css?v=3.0.9',
    ];

    /**
     * @var array<string>
     */
    protected static $js = [
        '/vendor/open-admin/bootstrap-duallistbox/dist/jquery.bootstrap-duallistbox.min.js?v=3.0.9',
    ];

    /**
     * @param array<mixed> $settings
     * @return $this
     */
    public function settings(array $settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Set listbox height.
     *
     * @param int $height
     *
     * @return Listbox
     */
    public function height($height = 100)
    {
        return $this->settings(['selectorMinimalHeight' => $height]);
    }

    /**
     * {@inheritdoc}
     * @param string $url
     * @param array<mixed> $parameters
     * @param array<mixed> $options
     * @return $this
     */
    protected function loadRemoteOptions($url, $parameters = [], $options = [])
    {
        $ajaxOptions = json_encode(array_merge([
            'url' => $url.'?'.http_build_query($parameters),
        ], $options));

        $this->script = <<<EOT
        
$.ajax($ajaxOptions).done(function(data) {

  var listbox = $("{$this->getElementClassSelector()}");

    var value = listbox.data('value') + '';
    
    if (value) {
      value = value.split(',');
    }
    
    for (var key in data) {
        var selected =  ($.inArray(key, value) >= 0) ? 'selected' : '';
        listbox.append('<option value="'+key+'" '+selected+'>'+data[key]+'</option>');
    }
    
    listbox.bootstrapDualListbox('refresh', true);
});
EOT;

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $settings = array_merge($this->settings, [
            'infoText'          => trans('admin.listbox.text_total'),
            'infoTextEmpty'     => trans('admin.listbox.text_empty'),
            'infoTextFiltered'  => trans('admin.listbox.filtered'),
            'filterTextClear'   => trans('admin.listbox.filter_clear'),
            'filterPlaceHolder' => trans('admin.listbox.filter_placeholder'),
            // Sometimes, click not working, so double click is false
            'moveOnDoubleClick' => false,
        ]);

        $settings = json_encode($settings);

        $this->script .= <<<SCRIPT

        var dualListBox = $("{$this->getElementClassSelector()}");
        dualListBox.bootstrapDualListbox($settings);
        var isRequiredField = dualListBox.attr('required');

        function initDualListBox() {
            var instance = dualListBox.data('plugin_bootstrapDualListbox');
            var nonSelectedList = instance.elements.select1;
            var isDualListBoxValidated = !(instance.selectedElements > 0);
    
            nonSelectedList.prop('required', isDualListBoxValidated);
            instance.elements.originalSelect.prop('required', false);
        }
    
        dualListBox.change(function () {
            if (isRequiredField)
                initDualListBox();
        });
    
        if (isRequiredField)
            initDualListBox();

SCRIPT;

        $this->attribute('data-value', implode(',', (array) $this->value()));

        return parent::render();
    }
}
