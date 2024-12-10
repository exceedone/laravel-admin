<?php

namespace OpenAdmin\Admin\Grid\Filter\Presenter;

use OpenAdmin\Admin\Facades\Admin;
use Illuminate\Contracts\Support\Arrayable;

class Radio extends Presenter
{
    /**
     * @var array<mixed>
     */
    protected $options = [];

    /**
     * Display inline.
     *
     * @var bool
     */
    protected $inline = true;

    /**
     * Radio constructor.
     *
     * @param array<mixed>|Arrayable<int|string, mixed>|null $options
     */
    public function __construct($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = (array) $options;
    }

    /**
     * Draw stacked radios.
     *
     * @return $this
     */
    public function stacked() : self
    {
        $this->inline = false;

        return $this;
    }

    /**
     * @return void
     */
    protected function prepare()
    {
        $script = "$('.{$this->filter->getId()}').iCheck({radioClass:'iradio_minimal-blue'});";

        Admin::script($script);
    }

    /**
     * @return array<string, mixed>
     */
    public function variables() : array
    {
        $this->prepare();

        return [
            'options' => $this->options,
            'inline'  => $this->inline,
        ];
    }
}
