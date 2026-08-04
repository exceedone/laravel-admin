<?php

namespace Encore\Admin\Grid\Filter;

use Illuminate\Support\Arr;

class NotEqual extends AbstractFilter
{
    /**
     * {@inheritdoc}
     *
     * @param array<mixed> $inputs
     *
     * @return mixed
     */
    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if (!isset($value)) {
            return;
        }

        // @phpstan-ignore-next-line Assigned value is always array|string at runtime
        $this->value = $value;

        return $this->buildCondition($this->column, '!=', $this->value);
    }
}
