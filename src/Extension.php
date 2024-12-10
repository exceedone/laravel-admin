<?php

namespace OpenAdmin\Admin;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

/**
 * @phpstan-consistent-constructor
 */
abstract class Extension
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array<mixed>
     */
    public $css = [];

    /**
     * @var array<mixed>
     */
    public $js = [];

    /**
     * @var string
     */
    public $assets = '';

    /**
     * @var string
     */
    public $views = '';

    /**
     * @var string
     */
    public $migrations = '';

    /**
     * @var array<mixed>
     */
    public $menu = [];

    /**
     * @var array<mixed>
     */
    public $permission = [];

    /**
     * Extension instance.
     *
     * @var Extension
     */
    protected static $instance;

    /**
     * The menu validation rules.
     *
     * @var array<string, string>
     */
    protected $menuValidationRules = [
        'title' => 'required',
        'path'  => 'required',
        'icon'  => 'required',
    ];

    /**
     * The permission validation rules.
     *
     * @var array<string, string>
     */
    protected $permissionValidationRules = [
        'name'  => 'required',
        'slug'  => 'required',
        'path'  => 'required',
    ];

    /**
     * Returns the singleton instance.
     *
     * @return self
     */
    protected static function getInstance()
    {
        $class = get_called_class();

        if (!isset(self::$instance[$class]) || !self::$instance[$class] instanceof $class) {
            /** @phpstan-ignore-next-line https://phpstan.org/blog/solving-phpstan-error-unsafe-usage-of-new-static */
            self::$instance[$class] = new static();
        }

        return static::$instance[$class];
    }

    /**
     * Bootstrap this extension.
     *
     * @return bool
     */
    public static function boot()
    {
        $extension = static::getInstance();

        Admin::extend($extension->name, get_called_class());

        if ($extension->disabled()) {
            return false;
        }

        if (!empty($css = $extension->css())) {
            Admin::css($css);
        }

        if (!empty($js = $extension->js())) {
            Admin::js($js);
        }

        return true;
    }

    /**
     * Get the path of assets files.
     *
     * @return string
     */
    public function assets()
    {
        return $this->assets;
    }

    /**
     * Get the paths of css files.
     *
     * @return array<mixed>
     */
    public function css()
    {
        return $this->css;
    }

    /**
     * Get the paths of js files.
     *
     * @return array<mixed>
     */
    public function js()
    {
        return $this->js;
    }

    /**
     * Get the path of view files.
     *
     * @return string
     */
    public function views()
    {
        return $this->views;
    }

    /**
     * Get the path of migration files.
     *
     * @return string
     */
    public function migrations()
    {
        return $this->migrations;
    }

    /**
     * @return array<mixed>
     */
    public function menu()
    {
        return $this->menu;
    }

    /**
     * @return array<mixed>
     */
    public function permission()
    {
        return $this->permission;
    }

    /**
     * Whether the extension is enabled.
     *
     * @return bool
     */
    public function enabled()
    {
        return static::config('enable') !== false;
    }

    /**
     * Whether the extension is disabled.
     *
     * @return bool
     */
    public function disabled()
    {
        return !$this->enabled();
    }

    /**
     * Get config set in config/admin.php.
     *
     * @param string $key
     * @param null|array<mixed>   $default
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function config($key = null, $default = null)
    {
        $name = array_search(get_called_class(), Admin::$extensions);

        if (is_null($key)) {
            $key = sprintf('admin.extensions.%s', strtolower($name));
        } else {
            $key = sprintf('admin.extensions.%s.%s', strtolower($name), $key);
        }

        return config($key, $default);
    }

    /**
     * Import menu item and permission to laravel-admin.
     * @return void
     */
    public static function import()
    {
        $extension = static::getInstance();

        if ($menu = $extension->menu()) {
            if ($extension->validateMenu($menu)) {
                $title = null;
                $path = null;
                $icon = null;
                extract($menu);
                static::createMenu($title, $path, $icon);
            }
        }

        if ($permission = $extension->permission()) {
            if ($extension->validatePermission($permission)) {
                $name = null;
                $slug = null;
                $path = null;
                extract($permission);
                static::createPermission($name, $slug, $path);
            }
        }
    }

    /**
     * Validate menu fields.
     *
     * @param array<mixed> $menu
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function validateMenu(array $menu)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($menu, $this->menuValidationRules);

        if ($validator->passes()) {
            return true;
        }

        $message = "Invalid menu:\r\n".implode("\r\n", Arr::flatten($validator->errors()->messages()));

        throw new \Exception($message);
    }

    /**
     * Validate permission fields.
     *
     *
     * @param array<mixed> $permission
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function validatePermission(array $permission)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($permission, $this->permissionValidationRules);

        if ($validator->passes()) {
            return true;
        }

        $message = "Invalid permission:\r\n".implode("\r\n", Arr::flatten($validator->errors()->messages()));

        throw new \Exception($message);
    }

    /**
     * Create a item in laravel-admin left side menu.
     *
     * @param string|null $title
     * @param string|null $uri
     * @param string|null  $icon
     * @param int    $parentId
     *
     * @return void
     */
    protected static function createMenu($title, $uri, $icon = 'fa-bars', $parentId = 0)
    {
        $menuModel = config('admin.database.menu_model');

        $lastOrder = $menuModel::max('order');

        $menuModel::create([
            'parent_id' => $parentId,
            'order'     => $lastOrder + 1,
            'title'     => $title,
            'icon'      => $icon,
            'uri'       => $uri,
        ]);
    }

    /**
     * Create a permission for this extension.
     *
     * @param null|mixed $name
     * @param null|mixed $slug
     * @param null|mixed $path
     *
     * @return void
     */
    protected static function createPermission($name, $slug, $path)
    {
        $permissionModel = config('admin.database.permissions_model');

        $permissionModel::create([
            'name'      => $name,
            'slug'      => $slug,
            'http_path' => '/'.trim($path, '/'),
        ]);
    }

    /**
     * Set routes for this extension.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public static function routes($callback)
    {
        $attributes = array_merge(
            [
                'prefix'     => config('admin.route.prefix'),
                'middleware' => config('admin.route.middleware'),
            ],
            static::config('route', [])
        );

        Route::group($attributes, $callback);
    }
}
