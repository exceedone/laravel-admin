<?php

namespace Encore\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Eloquent Model used SoftDeletes trait.
 * For phpstan reference
 * @deprecated
 */
class SoftDeletableModel extends Builder
{
    use SoftDeletes;
}
