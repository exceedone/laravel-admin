<?php

namespace OpenAdmin\Admin\Traits;

trait HasAssets
{
    /**
     * @var array<string>
     */
    public static $script = [];

    /**
     * @var array<string>
     */
    public static $style = [];

    /**
     * @var array<string>
     */
    public static $css = [];

    /**
     * @var array<string>
     */
    public static $csslast = [];

    /**
     * @var array<string>
     */
    public static $js = [];

    /**
     * @var array<string>
     */
    public static $jslast = [];

    /**
     * @var array<string>
     */
    public static $html = [];

    /**
     * @var array<string>
     */
    public static $headerJs = [];

    /**
     * @var string
     */
    public static $manifest = 'vendor/open-admin/minify-manifest.json';

    /**
     * @var array<mixed>
     */
    public static $manifestData = [];

    /**
     * @var array<string, string>
     */
    public static $min = [
        'js' => 'vendor/open-admin/open-admin.min.js',
        'css' => 'vendor/open-admin/open-admin.min.css',
    ];

    /**
     * @var array<string>
     */
    public static $baseCss = [
        // 'vendor/open-admin/AdminLTE/bootstrap/css/bootstrap.min.css',
        'vendor/open-admin/font-awesome/css/all.min.css',
        'vendor/open-admin/font-awesome/css/v4-shims.min.css',
        // 'vendor/open-admin/open-admin/open-admin.css',
        // 'vendor/open-admin/nprogress/nprogress.css',
        'vendor/open-admin/sweetalert2/dist/sweetalert2.css',
        // 'vendor/open-admin/nestable/nestable.css',
        // 'vendor/open-admin/toastr/build/toastr.min.css',
        // 'vendor/open-admin/bootstrap3-editable/css/bootstrap-editable.css',
        // 'vendor/open-admin/google-fonts/fonts.css',
        // 'vendor/open-admin/AdminLTE/dist/css/AdminLTE.min.css',
        'vendor/open-admin/nprogress/nprogress.css',
        'vendor/open-admin/sweetalert2/sweetalert2.min.css',
        'vendor/open-admin/toastify-js/toastify.css',
        'vendor/open-admin/flatpickr/flatpicker-custom.css',
        'vendor/open-admin/choicesjs/styles/choices.min.css',
        'vendor/open-admin/sortablejs/nestable.css',

        // custom open admin stuff
        // generated through sass
        'vendor/open-admin/open-admin/css/styles.css',
        'vendor/open-admin/AdminLTE4/css/adminlte.min.css',
        'vendor/open-admin/open-admin/css/custom.css',

        // "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css",
        // "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css",
        "https://cdn.jsdelivr.net/npm/bootstrap-fileinput/css/fileinput.min.css",

    ];

    /**
     * @var array<string>
     */
    public static $baseJs = [
        'vendor/open-admin/AdminLTE/bootstrap/js/bootstrap.min.js',
        'vendor/open-admin/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js',
        'vendor/open-admin/AdminLTE/dist/js/app.min.js',
        'vendor/open-admin/jquery-pjax/jquery.pjax.js',
        'vendor/open-admin/nprogress/nprogress.js',
        'vendor/open-admin/nestable/jquery.nestable.js',
        'vendor/open-admin/toastr/build/toastr.min.js',
        'vendor/open-admin/bootstrap3-editable/js/bootstrap-editable.min.js',
        'vendor/open-admin/sweetalert2/dist/sweetalert2.min.js',
        'vendor/open-admin/open-admin/open-admin.js',

        // 'vendor/open-admin/bootstrap5/bootstrap.bundle.min.js',
        // 'vendor/open-admin/nprogress/nprogress.js',
        // 'vendor/open-admin/axios/axios.min.js',
        // 'vendor/open-admin/sweetalert2/sweetalert2.min.js',
        // 'vendor/open-admin/toastify-js/toastify.js',
        // 'vendor/open-admin/flatpickr/flatpickr.min.js',
        // 'vendor/open-admin/choicesjs/scripts/choices.min.js',
        // 'vendor/open-admin/sortablejs/Sortable.min.js',

        // 'vendor/open-admin/open-admin/js/polyfills.js',
        // 'vendor/open-admin/open-admin/js/helpers.js',
        // 'vendor/open-admin/open-admin/js/open-admin.js',
        // 'vendor/open-admin/open-admin/js/open-admin-actions.js',
        // 'vendor/open-admin/open-admin/js/open-admin-grid.js',
        // 'vendor/open-admin/open-admin/js/open-admin-grid-inline-edit.js',
        // 'vendor/open-admin/open-admin/js/open-admin-form.js',
        // 'vendor/open-admin/open-admin/js/open-admin-toastr.js',
        // 'vendor/open-admin/open-admin/js/open-admin-resource.js',
        // 'vendor/open-admin/open-admin/js/open-admin-tree.js',
        // 'vendor/open-admin/open-admin/js/open-admin-selectable.js',

        // 'vendor/open-admin/jquery-pjax/jquery.pjax.js',
        // 'vendor/open-admin/toastr/build/toastr.min.js',
        // 'vendor/open-admin/bootstrap3-editable/js/bootstrap-editable.min.js',
        // 'vendor/laravel-admin/laravel-admin/laravel-admin.js',
        // 'vendor/open-admin/AdminLTE/bootstrap/js/bootstrap.min.js',
        // 'vendor/open-admin/AdminLTE4/js/adminlte.min.js',

        // "https://code.jquery.com/jquery-3.7.1.min.js",
        "https://cdn.jsdelivr.net/npm/bootstrap-fileinput/js/fileinput.min.js",
        "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js",
        "https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js",

    ];

    /**
     * @var string
     */
    public static $jQuery = 'vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js';

    /**
     * Add css or get all css.
     *
     * @param null|array<mixed> $css
     *
     * @return array<mixed>|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function css($css = null)
    {
        if (!is_null($css)) {
            return self::$css = array_merge(self::$css, (array) $css);
        }

        if (!$css = static::getMinifiedCss()) {
            $css = array_merge(static::$css, static::baseCss());
        }

        $css = array_merge($css, static::$csslast);

        $css = array_filter(array_unique($css));

        return view('admin::partials.css', compact('css'));
    }

    /**
     * @param null $css
     *
     * @return array<string>|null
     */
    public static function baseCss($css = null)
    {
        if (!is_null($css)) {
            return static::$baseCss = $css;
        }

        $skin = config('admin.skin', 'skin-blue-light');

        array_unshift(static::$baseCss, "vendor/open-admin/AdminLTE/dist/css/skins/{$skin}.min.css");

        return static::$baseCss;
    }

    /**
     * Add js or get all js.
     *
     * @param null|array<mixed> $js
     *
     * @return array<mixed>|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function js($js = null)
    {
        if (!is_null($js)) {
            return self::$js = array_merge(self::$js, (array) $js);
        }

        if (!$js = static::getMinifiedJs()) {
            $js = array_merge(static::baseJs(), static::$js);
        }

        $js = array_merge($js, static::$jslast);

        $js = array_filter(array_unique($js));

        return view('admin::partials.js', compact('js'));
    }

    /**
     * Add js or get all js.
     *
     * @param null $js
     *
     * @return array<mixed>|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function headerJs($js = null)
    {
        if (!is_null($js)) {
            return self::$headerJs = array_merge(self::$headerJs, (array) $js);
        }

        return view('admin::partials.js', ['js' => array_unique(static::$headerJs)]);
    }

    /**
     * @param null $js
     *
     * @return array<string>|null
     */
    public static function baseJs($js = null)
    {
        if (!is_null($js)) {
            return static::$baseJs = $js;
        }

        return static::$baseJs;
    }

    /**
     * Add css at end of array.
     *
     * @param null $css
     *
     * @return array<mixed>
     */
    public static function csslast($css)
    {
        return self::$csslast = array_merge(self::$csslast, (array) $css);
    }

    /**
     * Add js at end of array.
     *
     * @param null $js
     *
     * @return array<mixed>
     */
    public static function jslast($js)
    {
        return self::$jslast = array_merge(self::$jslast, (array) $js);
    }

    /**
     * @param string $script
     *
     * @return array<mixed>|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function script($script = '')
    {
        if (!empty($script)) {
            return self::$script = array_merge(self::$script, (array) $script);
        }

        return view('admin::partials.script', ['script' => array_unique(self::$script)]);
    }

    /**
     * get script. Pure script, ignore $(function), and script tag 
     *
     * @return \Illuminate\View\View
     */
    public static function purescript()
    {
        return view('admin::partials.purescript', ['script' => array_unique(self::$script)]);
    }

    /**
     * @param string $style
     *
     * @return array<mixed>|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function style($style = '')
    {
        if (!empty($style)) {
            return self::$style = array_merge(self::$style, (array) $style);
        }

        return view('admin::partials.style', ['style' => array_unique(self::$style)]);
    }

    /**
     * @param string $html
     *
     * @return array<mixed>|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function html($html = '')
    {
        if (!empty($html)) {
            return self::$html = array_merge(self::$html, (array) $html);
        }

        return view('admin::partials.html', ['html' => array_unique(self::$html)]);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected static function getManifestData($key)
    {
        if (!empty(static::$manifestData)) {
            return static::$manifestData[$key];
        }

        static::$manifestData = json_decode(
            file_get_contents(public_path(static::$manifest)),
            true
        );

        return static::$manifestData[$key];
    }

    /**
     * @return bool|mixed
     */
    protected static function getMinifiedCss()
    {
        if (!config('admin.minify_assets') || !file_exists(public_path(static::$manifest))) {
            return false;
        }

        return static::getManifestData('css');
    }

    /**
     * @return bool|mixed
     */
    protected static function getMinifiedJs()
    {
        if (!config('admin.minify_assets') || !file_exists(public_path(static::$manifest))) {
            return false;
        }

        return static::getManifestData('js');
    }

    /**
     * @return string
     */
    public function jQuery()
    {
        return admin_asset(static::$jQuery);
    }
}
