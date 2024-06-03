<?php

namespace Encore\Admin;

use Illuminate\Database\Eloquent\Builder;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Eloquent Model used SoftDeletes trait.
 * For phpstan reference
 * @deprecated
 */
class SortableModel extends Builder
{
    use SortableTrait;
}
