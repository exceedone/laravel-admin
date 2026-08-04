<?php

namespace Encore\Admin\Grid\Displayers;

class Modal extends AbstractDisplayer
{
    /**
     * @param mixed|null $callback
     * @return string
     */
    public function display($callback = null)
    {
        $title = '';
        if (func_num_args() == 2) {
            list($title, $callback) = func_get_args();
        } elseif (func_num_args() == 1) {
            $title = $this->trans('title');
        }

        // @phpstan-ignore-next-line The value is always an object exposing bindTo() at runtime
        $callback = $callback->bindTo($this->row);

        $html = call_user_func_array($callback, [$this->row]);

        $key = $this->getKey().'-'.$this->getColumn()->getName();

        // Type assertion for PHPStan - maintain original behavior
        /**
         * @var string $value
         * @var string $title
         * @var string $html
         */
        $value = $this->value;

        return <<<EOT
<span class="grid-expand" data-toggle="modal" data-target="#grid-modal-{$key}">
   <a href="javascript:void(0)"><i class="fa fa-clone"></i>&nbsp;&nbsp;{$value}</a>
</span>

<div class="modal fade" id="grid-modal-{$key}" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{$title}</h4>
      </div>
      <div class="modal-body">
        {$html}
      </div>
    </div>
  </div>
</div>

EOT;
    }
}
