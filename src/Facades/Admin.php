<?php

namespace OpenAdmin\Admin\Facades;

use OpenAdmin\Admin\Auth\Database\Administrator;
use OpenAdmin\Admin\Auth\Database\HasPermissions;
use Illuminate\Support\Facades\Facade;

/**
 * Class Admin.
 *
 * @method static \OpenAdmin\Admin\Grid grid($model, \Closure $callable)
 * @method static \OpenAdmin\Admin\Form form($model, \Closure $callable)
 * @method static \OpenAdmin\Admin\Show show($model, $callable = null)
 * @method static \OpenAdmin\Admin\Tree tree($model, \Closure $callable = null)
 * @method static \OpenAdmin\Admin\Layout\Content content(\Closure $callable = null)
 * @method static \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void css($css = null)
 * @method static \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void js($js = null)
 * @method static \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void headerJs($js = null)
 * @method static \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void script($script = '')
 * @method static \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void style($style = '')
 * @method static Administrator|null user()
 * @method static string title()
 * @method static void navbar(\Closure $builder = null)
 * @method static void registerAuthRoutes()
 * @method static void extend($name, $class)
 * @method static void disablePjax()
 * @method static void booting(\Closure $builder)
 * @method static void booted(\Closure $builder)
 * @method static void bootstrap()
 *
 * @see \OpenAdmin\Admin\Admin
 */
class Admin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \OpenAdmin\Admin\Admin::class;
    }
}
