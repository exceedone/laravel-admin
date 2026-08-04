<?php

namespace Encore\Admin\Grid\Displayers;

use Encore\Admin\Admin;
use Illuminate\Support\Arr;

class SwitchDisplay extends AbstractDisplayer
{

    /**
     * @var array<string, array{value: mixed, text: string, color: string}> $states
     */
    protected $states = [
        'on'  => ['value' => 1, 'text' => 'ON', 'color' => 'primary'],
        'off' => ['value' => 0, 'text' => 'OFF', 'color' => 'default'],
    ];

    /**
     * @param array<string, array<string, mixed>> $states
     *
     * @return void
     */
    protected function updateStates($states)
    {
        foreach (Arr::dot($states) as $key => $state) {
            Arr::set($this->states, $key, $state);
        }
    }

    /**
     * @param array<string, array<string,mixed>> $states
     *
     * @return string
     */
    public function display($states = [])
    {
        $this->updateStates($states);

        $name = $this->column->getName();

        // @phpstan-ignore-next-line $subject is always array|string at runtime
        $class = 'grid-switch-'.str_replace('.', '-', $name);

        // @phpstan-ignore-next-line $string is always string at runtime
        $keys = collect(explode('.', $name));
        if ($keys->isEmpty()) {
            $key = $name;
        } else {
            $key = $keys->shift().$keys->reduce(function ($carry, $val) {
                return $carry."[$val]";
            });
        }

        // Type assertion for PHPStan - maintain original behavior
        /**
         * @var string $resource
         * @var string $key
         */
        $resource = $this->grid->resource();
        
        $script = <<<EOT

$('.$class').bootstrapSwitch({
    size:'mini',
    onText: '{$this->states['on']['text']}',
    offText: '{$this->states['off']['text']}',
    onColor: '{$this->states['on']['color']}',
    offColor: '{$this->states['off']['color']}',
    onSwitchChange: function(event, state){

        $(this).val(state ? 'on' : 'off');

        var pk = $(this).data('key');
        var value = $(this).val();
        var _status = true;

        $.ajax({
            url: "{$resource}/" + pk,
            type: "POST",
            async:false,
            data: {
                "$key": value,
                _token: LA.token,
                _method: 'PUT'
            },
            success: function (data) {
                if (data.status)
                    toastr.success(data.message);
                else
                    toastr.warning(data.message);
            },
            complete:function(xhr,status) {
                if (status == 'success')
                    _status = xhr.responseJSON.status;
            }
        });
        
        return _status;
    }
});

EOT;

        Admin::script($script);

        $key = $this->row->{$this->grid->getKeyName()};

        $checked = $this->states['on']['value'] == $this->value ? 'checked' : '';

        return <<<EOT
        <input type="checkbox" class="$class" $checked data-key="$key" />
EOT;
    }
}
