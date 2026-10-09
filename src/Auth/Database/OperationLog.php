<?php

namespace Encore\Admin\Auth\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static mixed create(array $attributes)
 */
class OperationLog extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'path', 'method', 'ip', 'input'];

    /**
     * @var array<string, string>
     */
    public static $methodColors = [
        'GET'    => 'green',
        'POST'   => 'yellow',
        'PUT'    => 'blue',
        'DELETE' => 'red',
    ];

    /**
     * @var array<int, string>
     */
    public static $methods = [
        'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH',
        'LINK', 'UNLINK', 'COPY', 'HEAD', 'PURGE',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array<mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        // @phpstan-ignore-next-line $name is always string|null at runtime
        $this->setConnection($connection);

        // @phpstan-ignore-next-line $table is always string at runtime
        $this->setTable(config('admin.database.operation_log_table'));

        parent::__construct($attributes);
    }

    /**
     * Log belongs to users.
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        // @phpstan-ignore-next-line $related is always string at runtime
        return $this->belongsTo(config('admin.database.users_model'));
    }
}
