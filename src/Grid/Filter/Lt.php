<?php

namespace OpenAdmin\Admin\Grid\Filter;

use Illuminate\Support\Arr;

class Lt extends AbstractFilter
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $view = 'admin::filter.lt';

    /**
     * Get condition of this filter.
     *
     * @param array<mixed> $inputs
     *
     * @return array<mixed>|mixed|void
     */
    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if (is_null($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, '<=', $this->value);
    }
}
