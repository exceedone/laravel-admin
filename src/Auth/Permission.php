<?php

namespace Encore\Admin\Auth;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Middleware\Pjax;
use Illuminate\Support\Facades\Auth;

class Permission
{

    /**
     * Check permission.
     *
     * @param $permission
     * @return true|void
     */
    public static function check($permission)
    {
        if (static::isAdministrator()) {
            return true;
        }

        if (is_array($permission)) {
            collect($permission)->each(function ($permission) {
                call_user_func([self::class, 'check'], $permission);
            });

            return;
        }

        if (Auth::guard('admin')->user()->cannot($permission)) {
            static::error();
        }
    }

    /**
     * Roles allowed to access.
     *
     * @param $roles
     * @return true|void
     */
    public static function allow($roles)
    {
        if (static::isAdministrator()) {
            return true;
        }

        // @phpstan-ignore-next-line inRoles undefined
        if (!Auth::guard('admin')->user()->inRoles($roles)) {
            static::error();
        }
    }

    /**
     * Don't check permission.
     *
     * @return bool
     */
    public static function free()
    {
        return true;
    }

    /**
     * Roles denied to access.
     *
     * @param $roles
     * @return true|void
     */
    public static function deny($roles)
    {
        if (static::isAdministrator()) {
            return true;
        }

        // @phpstan-ignore-next-line inRoles undefined
        if (Auth::guard('admin')->user()->inRoles($roles)) {
            static::error();
        }
    }

    /**
     * Send error response page.
     */
    public static function error($message = null)
    {
        if(empty($message)){
            $message = trans('admin.deny');
        }
        
        // move to after ajax
        //$response = response(Admin::content()->withError($message));

        if (!request()->pjax() && request()->ajax()) {
            abort(403, $message);
        }

        $response = response(Admin::content()->withError($message));

        Pjax::respond($response);
    }

    /**
     * If current user is administrator.
     *
     * @return mixed
     */
    public static function isAdministrator()
    {
        // @phpstan-ignore-next-line isRole undefined
        return Auth::guard('admin')->user()->isRole('administrator');
    }
}
