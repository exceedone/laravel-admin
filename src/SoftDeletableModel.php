<?php

namespace OpenAdmin\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Eloquent Model used SoftDeletes trait.
 * For phpstan reference
 * @deprecated
 * @phpstan-ignore-next-line Class OpenAdmin\Admin\SoftDeletableModel extends generic class Illuminate\Database\Eloquent\Builder but does not specify its types: TModelClass
 */
class SoftDeletableModel extends Builder
{
    use SoftDeletes;
}
