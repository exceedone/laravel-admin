<?php

namespace Encore\Admin\Grid\Displayers;

use Illuminate\Support\Arr;

class Table extends AbstractDisplayer
{
    /**
     * @param array<mixed> $titles
     * @return mixed|string
     */
    public function display($titles = [])
    {
        if (empty($this->value)) {
            return '';
        }

        if (empty($titles)) {
            // @phpstan-ignore-next-line The value is always an array at runtime (and 1 more mixed-type assumption on this line)
            $titles = array_keys($this->value[0]);
        }

        if (Arr::isAssoc($titles)) {
            $columns = array_keys($titles);
        } else {
            // @phpstan-ignore-next-line $keys is always array<int|string> at runtime
            $titles = array_combine($titles, $titles);
            $columns = $titles;
        }

        $data = array_map(function ($item) use ($columns) {
            $sorted = [];

            // @phpstan-ignore-next-line $array is always array at runtime
            $arr = Arr::only($item, $columns);

            foreach ($columns as $column) {
                // @phpstan-ignore-next-line $key is always int|string at runtime
                if (array_key_exists($column, $arr)) {
                    $sorted[$column] = $arr[$column];
                }
            }

            return $sorted;
        // @phpstan-ignore-next-line $array is always array at runtime
        }, $this->value);

        $variables = [
            'titles' => $titles,
            'data'   => $data,
        ];

        return view('admin::grid.displayer.table', $variables)->render();
    }
}
