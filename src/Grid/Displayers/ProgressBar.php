<?php

namespace Encore\Admin\Grid\Displayers;

class ProgressBar extends AbstractDisplayer
{
    /**
     * @param string $style
     * @param string $size
     * @param int $max
     * @return string
     */
    public function display($style = 'primary', $size = '', $max = 100)
    {
        $style = collect((array) $style)->map(function ($style) {
            return 'progress-bar-'.$style;
        })->implode(' ');

        // Type assertion for PHPStan - maintain original behavior
        /** @var string $value */
        $value = $this->value;

        return <<<EOT

<div class="progress progress-$size">
    <div class="progress-bar $style" role="progressbar" aria-valuenow="{$value}" aria-valuemin="0" aria-valuemax="$max" style="width: {$value}%">
      <span>{$value}</span>
    </div>
</div>

EOT;
    }
}
