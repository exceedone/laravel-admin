<?php

namespace OpenAdmin\Admin\Form\Field;

use OpenAdmin\Admin\Admin;

class Fieldset
{
    /**
     * @var string
     */
    protected $name = '';

    public function __construct()
    {
        $this->name = uniqid('fieldset-');
    }

    /**
     * Start a fieldset.
     *
     * @param string $title
     *
     * @return string
     */
    public function start($title)
    {
        $script = <<<SCRIPT
$('.{$this->name}-title').click(function () {
    $("i", this).toggleClass("fa-angle-double-down fa-angle-double-up");
});
SCRIPT;

        Admin::script($script);

        return <<<HTML
<div>
    <div style="height: 20px; border-bottom: 1px solid #eee; text-align: center;margin-top: 20px;margin-bottom: 20px;">
      <span style="font-size: 16px; background-color: #ffffff; padding: 0 10px;">
        <a data-toggle="collapse" href="#{$this->name}" class="{$this->name}-title">
          <i class="fa fa-angle-double-up"></i>&nbsp;&nbsp;{$title}
        </a>
      </span>
    </div>
    <div class="collapse in" id="{$this->name}">
HTML;
    }

    /**
     * @return string
     */
    public function end()
    {
        return '</div></div>';
    }

    /**
     * Collapsed fieldset.
     *
     *
     * @return $this
     */
    public function collapsed()
    {
        $script = <<<SCRIPT
$("#{$this->name}").removeClass("in");
$(".{$this->name}-title i").toggleClass("fa-angle-double-down fa-angle-double-up");
SCRIPT;

        Admin::script($script);

        return $this;
    }
}
