<?php

namespace Encore\Admin\Grid\Displayers;

use Encore\Admin\Admin;

class Select extends AbstractDisplayer
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

        // Type assertion for PHPStan - maintain original behavior
        /** @var string $name */
        $name = $this->column->getName();

        $class = "grid-select-{$name}";
        
        // Type assertion for PHPStan - maintain original behavior
        /** @var string $resource */
        $resource = $this->grid->resource();

        $script = <<<EOT

$('.$class').select2().on('change', function(){

    var pk = $(this).data('key');
    var value = $(this).val();

    $.ajax({
        url: "{$resource}/" + pk,
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
});

EOT;

        Admin::script($script);

        $key = $this->row->{$this->grid->getKeyName()};

        $optionsHtml = '';

        // @phpstan-ignore-next-line The value is always iterable at runtime
        foreach ($options as $option => $text) {
            $selected = $option == $this->value ? 'selected' : '';
            // @phpstan-ignore-next-line $option is always castable to string at runtime (and 1 more mixed-type assumption on this line)
            $optionsHtml .= "<option value=\"$option\" $selected>$text</option>";
        }

        return <<<EOT
<select style="width: 100%;" class="$class btn btn-mini" data-key="$key">
$optionsHtml
</select>

EOT;
    }
}
