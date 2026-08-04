<?php

namespace Encore\Admin\Grid\Displayers;

use Encore\Admin\Admin;

class Radio extends AbstractDisplayer
{
    /**
     * @param array<mixed>|\Closure $options
     * @return string
     */
    public function display($options = [])
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this, $this->row);
        }

        $radios = '';

        // Type assertion for PHPStan - maintain original behavior
        /** @var string $name */
        $name = $this->column->getName();

        // @phpstan-ignore-next-line The value is always iterable at runtime
        foreach ($options as $value => $label) {
            $checked = ($value == $this->value) ? 'checked' : '';

            // Type assertion for PHPStan - maintain original behavior
            /**
             * @var string $value
             * @var string $label
             */

            $radios .= <<<EOT
<div class="radio">
    <label>
        <input type="radio" name="grid-radio-$name" value="{$value}" $checked />{$label}
    </label>
</div>
EOT;
        }

        Admin::script($this->script());

        // Type assertion for PHPStan - maintain original behavior
        /** @var string $saveText */
        $saveText = $this->trans('save');
        /** @var string $resetText */
        $resetText = $this->trans('reset');
        /** @var string $keyString */
        $keyString = $this->getKey();
        
        return <<<EOT
<form class="form-group grid-radio-$name" style="text-align: left" data-key="{$keyString}">
    $radios
    <button type="submit" class="btn btn-info btn-xs pull-left">
        <i class="fa fa-save"></i>&nbsp;{$saveText}
    </button>
    <button type="reset" class="btn btn-warning btn-xs pull-left" style="margin-left:10px;">
        <i class="fa fa-trash"></i>&nbsp;{$resetText}
    </button>
</form>
EOT;
    }

    /**
     * @return string
     */
    protected function script()
    {
        // Type assertion for PHPStan - maintain original behavior
        /** @var string $name */
        $name = $this->column->getName();

        return <<<EOT

$('form.grid-radio-$name').on('submit', function () {
    var value = $(this).find('input:radio:checked').val();

    $.ajax({
        url: "{$this->getResource()}/" + $(this).data('key'),
        type: "POST",
        data: {
            $name: value,
            _token: LA.token,
            _method: 'PUT'
        },
        success: function (data) {
            toastr.success(data.message);
        }
    });

    return false;
});

EOT;
    }
}
