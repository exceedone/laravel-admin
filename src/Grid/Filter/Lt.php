<?php

namespace Encore\Admin\Grid\Filter;

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

        // @phpstan-ignore-next-line Assigned value is always array|string at runtime
        $this->value = $value;

        return $this->buildCondition($this->column, '<=', $this->value);
    }
}
