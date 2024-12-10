<?php

namespace OpenAdmin\Admin\Grid\Displayers;

use OpenAdmin\Admin\Admin;

class Expand extends AbstractDisplayer
{
    /**
     * @param null|\Closure $callback
     * @return string
     */
    public function display($callback = null)
    {
        $callback = $callback->bindTo($this->row);

        $html = call_user_func_array($callback, [$this->row]);

        $this->setupScript();

        $key = $this->column->getName().'-'.$this->getKey();

        return <<<EOT
<span class="grid-expand" data-inserted="0" data-key="{$key}" data-toggle="collapse" data-target="#grid-collapse-{$key}">
   <a href="javascript:void(0)"><i class="fa fa-angle-double-down"></i>&nbsp;&nbsp;{$this->value}</a>
</span>
<template class="grid-expand-{$key}">
    <div id="grid-collapse-{$key}" class="collapse">
        <div  style="padding: 10px 10px 0 10px;">$html</div>
    </div>
</template>
EOT;
    }

    /**
     * @return void
     */
    protected function setupScript()
    {
        $script = <<<'EOT'

$('.grid-expand').on('click', function () {
    
    if ($(this).data('inserted') == '0') {
    
        var key = $(this).data('key');
        var row = $(this).closest('tr');
        var html = $('template.grid-expand-'+key).html();

        row.after("<tr style='background-color: #ecf0f5;'><td colspan='"+(row.find('td').length)+"' style='padding:0 !important; border:0;'>"+html+"</td></tr>");

        $(this).data('inserted', 1);
    }
    
    $("i", this).toggleClass("fa-angle-double-down fa-angle-double-up");
});
EOT;
        Admin::script($script);
    }
}
