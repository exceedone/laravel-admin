<?php

namespace Encore\Admin\Grid\Filter;

use Illuminate\Support\Arr;

class Like extends AbstractFilter
{
    /**
     * @var string
     */
    protected $exprFormat = '%{value}%';

    /**
     * @var string
     */
    protected $operator = 'like';

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

        if (is_array($value)) {
            $value = array_filter($value);
        }

        // @phpstan-ignore-next-line $string is always string at runtime
        if (is_null($value) || strlen($value) === 0) {
            return;
        }

        // @phpstan-ignore-next-line Assigned value is always array|string at runtime
        $this->value = $value;

        // @phpstan-ignore-next-line $replace is always array|string at runtime
        $expr = str_replace('{value}', $this->value, $this->exprFormat);

        return $this->buildCondition($this->column, $this->operator, $expr);
    }
}
