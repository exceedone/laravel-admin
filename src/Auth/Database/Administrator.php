<?php

namespace Encore\Admin\Auth\Database;

use Encore\Admin\Traits\AdminBuilder;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

/**
 * Class Administrator.
 *
 * @property Collection $roles
 * @phpstan-consistent-constructor
 */
class Administrator extends Model implements AuthenticatableContract
{
    use Authenticatable, AdminBuilder, HasPermissions;

    protected $fillable = ['username', 'password', 'name', 'avatar'];

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
        $this->setTable(config('admin.database.users_table'));

        parent::__construct($attributes);
    }

    /**
     * Get avatar attribute.
     *
     * @param string $avatar
     *
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        if (url()->isValidUrl($avatar)) {
            return $avatar;
        }

        $disk = config('admin.upload.disk');

        // @phpstan-ignore-next-line $key is always int|string at runtime (and 1 more mixed-type assumption on this line)
        if ($avatar && array_key_exists($disk, config('filesystems.disks'))) {
            // @phpstan-ignore-next-line $name is always string|null at runtime
            return Storage::disk(config('admin.upload.disk'))->url($avatar);
        }

        $default = config('admin.default_avatar') ?: '/vendor/laravel-admin/AdminLTE/dist/img/user2-160x160.jpg';

        // @phpstan-ignore-next-line $path is always string at runtime
        return admin_asset($default);
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles() : BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        // @phpstan-ignore-next-line $related is always string at runtime (and 1 more mixed-type assumption on this line)
        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }

    /**
     * A User has and belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions() : BelongsToMany
    {
        $pivotTable = config('admin.database.user_permissions_table');

        $relatedModel = config('admin.database.permissions_model');

        // @phpstan-ignore-next-line $related is always string at runtime (and 1 more mixed-type assumption on this line)
        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'permission_id');
    }
}
