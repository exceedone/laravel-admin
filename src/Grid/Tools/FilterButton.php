<?php

namespace OpenAdmin\Admin\Grid\Tools;

use OpenAdmin\Admin\Admin;

class FilterButton extends AbstractTool
{
    /**
     * @var string
     */
    protected $view = 'admin::filter.button';

    /**
     * @var string
     */
    protected $btnClassName;

    /**
     * @return \OpenAdmin\Admin\Grid\Filter
     */
    protected function filter()
    {
        return $this->grid->getFilter();
    }

    /**
     * Get button class name.
     *
     * @return string
     */
    protected function getElementClassName()
    {
        if (!$this->btnClassName) {
            $this->btnClassName = uniqid().'-filter-btn';
        }

        return $this->btnClassName;
    }

    /**
     * Set up script for export button.
     * @return void
     */
    protected function setUpScripts()
    {
        $id = $this->filter()->getFilterID();
        $filterAjax = $this->filter()->getFilterAjax();

        $script = <<<SCRIPT
        let target = $('.{$this->getElementClassName()}');
        target.unbind('click');
        target.click(function (e) {
    if ($('#{$id}').is(':visible')) {
        $('#{$id}').addClass('hide');
    } else {
        if('$filterAjax'.length > 0){
            if(target.attr('disabled')){
                return;
            }
            if(target.hasClass('loaded')){
                $('#{$id}').removeClass('hide');
                return;
            }
            
            var spinner = target.attr('disabled', true).data('loading-text');
            target.append(spinner);
            $.ajax({
                url:'$filterAjax',
                type: "GET",
                contentType: 'application/json;charset=utf-8',
                success: function (data) {
                    $('#{$id}').html($(data.html).children('form'));
                    eval(data.script);

                    target.attr('disabled', false).addClass('loaded');
                    target.find('.fa-spinner').remove();
                    $('#{$id}').removeClass('hide');
                }
            });
        }else{
            $('#{$id}').removeClass('hide');
        }
    }
});
SCRIPT;

        Admin::script($script);
    }

    /**
     * @return mixed
     */
    protected function renderScopes()
    {
        return $this->filter()->getScopes()->map->render()->implode("\r\n");
    }

    /**
     * Get label of current scope.
     *
     * @return string
     */
    protected function getCurrentScopeLabel()
    {
        if ($scope = $this->filter()->getCurrentScope()) {
            return "&nbsp;{$scope->getLabel()}&nbsp;";
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->setUpScripts();

        $variables = [
            'scopes'        => $this->filter()->getScopes(),
            'current_label' => $this->getCurrentScopeLabel(),
            'url_no_scopes' => $this->filter()->urlWithoutScopes(),
            'btn_class'     => $this->getElementClassName(),
            'expand'        => $this->filter()->expand,
        ];

        return view($this->view, $variables)->render();
    }
}
