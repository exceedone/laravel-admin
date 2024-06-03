<?php

use Illuminate\Support\MessageBag;

if (!function_exists('admin_path')) {

    /**
     * Get admin path.
     *
     * @param string $path
     *
     * @return string
     */
    function admin_path($path = '')
    {
        return ucfirst(config('admin.directory')).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('admin_url')) {
    /**
     * Get admin url.
     *
     * @param string $path
     * @param mixed  $parameters
     * @param bool   $secure
     *
     * @return string
     */
    function admin_url($path = '', $parameters = [], $secure = null)
    {
        if (\Illuminate\Support\Facades\URL::isValidUrl($path)) {
            return $path;
        }
        
        if(!isset($secure)){
            if(\Request::secure() === true){
                $secure = true;
            }
            else{
                $secure = (config('admin.https') || config('admin.secure'));
            }
        }

        if(boolval($secure)){
            \URL::forceScheme('https');
        }
        if(boolval(config('admin.use_app_url', false))){
            \URL::forceRootUrl(config('app.url'));
        }

        return url(admin_base_path($path), $parameters, $secure);
    }
}

if (!function_exists('admin_base_path')) {
    /**
     * Get admin url.
     *
     * @param string|null $path
     *
     * @return string
     */
    function admin_base_path($path = '')
    {
        $prefix = '/'.trim(config('admin.route.prefix'), '/');

        $prefix = ($prefix == '/') ? '' : $prefix;

        $path = trim($path?? '', '/');

        if (is_null($path) || strlen($path) == 0) {
            return $prefix ?: '/';
        }

        return $prefix.'/'.$path;
    }
}

if (!function_exists('admin_toastr')) {

    /**
     * Flash a toastr message bag to session.
     *
     * @param string $message
     * @param string $type
     * @param array  $options
     */
    function admin_toastr($message = '', $type = 'success', $options = [])
    {
        $toastr = new MessageBag(get_defined_vars());

        session()->flash('toastr', $toastr);
    }
}

if (!function_exists('admin_success')) {

    /**
     * Flash a success message bag to session.
     *
     * @param string $title
     * @param string $message
     */
    function admin_success($title, $message = '')
    {
        admin_info($title, $message, 'success');
    }
}

if (!function_exists('admin_error')) {

    /**
     * Flash a error message bag to session.
     *
     * @param string $title
     * @param string $message
     */
    function admin_error($title, $message = '')
    {
        admin_info($title, $message, 'error');
    }
}

if (!function_exists('admin_warning')) {

    /**
     * Flash a warning message bag to session.
     *
     * @param string $title
     * @param string $message
     */
    function admin_warning($title, $message = '')
    {
        admin_info($title, $message, 'warning');
    }
}

if (!function_exists('admin_info')) {

    /**
     * Flash a message bag to session.
     *
     * @param string $title
     * @param string $message
     * @param string $type
     */
    function admin_info($title, $message = '', $type = 'info')
    {
        $message = new MessageBag(get_defined_vars());

        session()->flash($type, $message);
    }
}


if (!function_exists('admin_error_once')) {

    /**
     * Flash a error message bag to session once.
     *
     * @param string $title
     * @param string $message
     */
    function admin_error_once($title, $message = '')
    {
        admin_info_once($title, $message, 'error');
    }
}

if (!function_exists('admin_warning_once')) {

    /**
     * Flash a warning message bag to session once.
     *
     * @param string $title
     * @param string $message
     */
    function admin_warning_once($title, $message = '')
    {
        admin_info_once($title, $message, 'warning');
    }
}

if (!function_exists('admin_info_once')) {

    /**
     * Flash a message bag to session once.
     *
     * @param string $title
     * @param string $message
     * @param string $type
     */
    function admin_info_once($title, $message = '', $type = 'info')
    {
        $message = new MessageBag(get_defined_vars());

        View::share("alert_$type", $message);
    }
}

if (!function_exists('admin_asset')) {

    /**
     * @param $path
     *
     * @return string
     */
    function admin_asset($path)
    {
        return (config('admin.https') || config('admin.secure')) ? secure_asset($path) : asset($path);
    }
}

if (!function_exists('admin_trans')) {

    /**
     * Translate the given message.
     *
     * @param string $key
     * @param array  $replace
     * @param string $locale
     *
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function admin_trans($key = null, $replace = [], $locale = null)
    {
        $line = __($key, $replace, $locale);

        if (!is_string($line)) {
            return $key;
        }

        return $line;
    }
}

if (!function_exists('array_delete')) {

    /**
     * Delete from array by value.
     *
     * @param array $array
     * @param mixed $value
     */
    function array_delete(&$array, $value)
    {
        foreach ($array as $index => $item) {
            if ($value == $item) {
                unset($array[$index]);
            }
        }
    }
}

if (!function_exists('class_uses_deep')) {

    /**
     * To get ALL traits including those used by parent classes and other traits.
     *
     * @param $class
     * @param bool $autoload
     *
     * @return array
     */
    function class_uses_deep($class, $autoload = true)
    {
        $traits = [];

        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));

        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique($traits);
    }
}

if (!function_exists('admin_dump')) {

    /**
     * @param $var
     *
     * @return string
     */
    function admin_dump($var)
    {
        ob_start();

        dump(...func_get_args());

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }
}

if (!function_exists('file_size')) {

    /**
     * Convert file size to a human readable format like `100mb`.
     *
     * @param int $bytes
     *
     * @return string
     *
     * @see https://stackoverflow.com/a/5501447/9443583
     */
    function file_size($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2).' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes.' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes.' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}

if (!function_exists('prepare_options')) {

    /**
     * @param array $options
     *
     * @return array
     */
    function prepare_options(array $options)
    {
        $original = [];
        $toReplace = [];

        foreach ($options as $key => &$value) {
            if (is_array($value)) {
                $subArray = prepare_options($value);
                $value = $subArray['options'];
                $original = array_merge($original, $subArray['original']);
                $toReplace = array_merge($toReplace, $subArray['toReplace']);
            } elseif (strpos($value, 'function(') === 0) {
                $original[] = $value;
                $value = "%{$key}%";
                $toReplace[] = "\"{$value}\"";
            }
        }

        return compact('original', 'toReplace', 'options');
    }
}

if (!function_exists('json_encode_options')) {

    /**
     * @param array $options
     *
     * @return string
     *
     * @see http://web.archive.org/web/20080828165256/http://solutoire.com/2008/06/12/sending-javascript-functions-over-json/
     */
    function json_encode_options(array $options)
    {
        $data = prepare_options($options);

        $json = json_encode($data['options']);

        return str_replace($data['toReplace'], $data['original'], $json);
    }
    
    if (!function_exists('strcmp_ex')) {
        /**
         * Wrapper for strcmp that throws when an error occurs.
         *
         * @param ?string $string1  compare string data 1
         * @param ?string $string2  compare string data 2
         *
         * @return int
         */
        function strcmp_ex(?string $string1, ?string $string2): int
        {
            $string1 = $string1?? '';
            $string2 = $string2?? '';

            return strcmp($string1, $string2);
        }
    }
    
    if (!function_exists('strpos_ex')) {
        /**
         * Wrapper for strpos that throws when an error occurs.
         *
         * @param ?string $haystack string to search
         * @param string  $needle   search string
         * @param int     $offset   search start position in the string
         *
         * @return int|false
         */
        function strpos_ex(?string $haystack, string $needle, int $offset = 0): int|false
        {
            $haystack = $haystack?? '';

            return strpos($haystack, $needle, $offset);
        }
    }
    
    if (!function_exists('strlen_ex')) {
        /**
         * Wrapper for strlen that throws when an error occurs.
         *
         * @param ?string $string  String to check the length
         *
         * @return int
         */
        function strlen_ex(?string $string): int
        {
            $string = $string?? '';

            return strlen($string);
        }
    }
    
    if (!function_exists('ucfirst_ex')) {
        /**
         * Wrapper for ucfirst that throws when an error occurs.
         *
         * @param ?string $string  input string
         *
         * @return string
         */
        function ucfirst_ex(?string $string): string
        {
            $string = $string?? '';

            return ucfirst($string);
        }
    }
    
    if (!function_exists('explode_ex')) {
        /**
         * Wrapper for explode that throws when an error occurs.
         *
         * @param string $separator Delimited string
         * @param string|null $string Input string
         * @param int $limit Number of elements in the return array
         * @return array
         */
        function explode_ex(string $separator, ?string $string, int $limit = PHP_INT_MAX): array
        {
            $string = $string?? '';

            return explode($separator, $string, $limit);
        }
    }
    
    if (!function_exists('parse_url_ex')) {
        /**
         * Wrapper for parse_url that throws when an error occurs.
         *
         * @param string|null $url parse target URL
         * @param int $component get only a specific URL component
         * @return int|string|array|false|null
         */
        function parse_url_ex(?string $url, int $component = -1): int|string|array|null|false
        {
            $url = $url?? '';

            return parse_url($url, $component);
        }
    }
    
    if (!function_exists('htmlentities_ex')) {
        /**
         * Wrapper for htmlentities that throws when an error occurs.
         *
         * @param ?string $string   input value
         * @param int     $flags    A bit mask that combines flags. 
         *                          Specifies quotes, invalid code unit sequences, and document type handling.
         * @param ?string $encoding Defines the encoding used when converting characters.
         * @param bool    $double_encode If double_encode is turned off, PHP will not encode existing html entities.
         *
         * @return string
         */
        function htmlentities_ex(
            ?string $string,
            int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401,
            ?string $encoding = null,
            bool $double_encode = true): string
        {
            $string = $string?? '';

            return htmlentities($string, $flags, $encoding, $double_encode);
        }
    }
    
    
    if (!function_exists('htmlspecialchars_ex')) {
        /**
         * Wrapper for htmlspecialchars that throws when an error occurs.
         *
         * @param ?string $string   input value
         * @param int     $flags    A bit mask that combines flags. 
         *                          Specifies quotes, invalid code unit sequences, and document type handling.
         * @param ?string $encoding Defines the encoding used when converting characters.
         * @param bool    $double_encode If double_encode is turned off, PHP will not encode existing html entities.
         *
         * @return string
         */
        function htmlspecialchars_ex(
            ?string $string,
            int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401,
            ?string $encoding = null,
            bool $double_encode = true
        ): string
        {
            $string = $string?? '';

            return htmlspecialchars($string, $flags, $encoding, $double_encode);
        }
    }
    
    if (!function_exists('str_replace_ex')) {
        /**
         * Wrapper for str_replace that throws when an error occurs.
         *
         * @param array|string $search search string or array
         * @param array|string|null $replace replace string or array
         * @param string|array|null $subject string or array to be searched / replaced.
         * @param int|null $count The number of matched and replaced locations is stored here.
         * @return string|array
         */
        function str_replace_ex(
            array|string $search,
            array|string $replace = null,
            string|array $subject = null,
            int &$count = null
        ): string|array
        {
            $replace = $replace?? '';
            $subject = $subject?? '';
            return str_replace($search, $replace, $subject, $count);
        }
    }
    
    if (!function_exists('preg_match_ex')) {
        /**
         * Wrapper for preg_match that throws when an error occurs.
         *
         * @param string  $pattern A string that represents the pattern to search for
         * @param ?string $subject input value
         * @param array   $matches If matches is specified, the search results will be assigned. 
         * @param int     $flags   mattching type flags. 
         * @param int     $offset  Specify the start position of the search (in bytes)
         *
         * @return int|false
         */
        function preg_match_ex(
            string $pattern,
            ?string $subject,
            array &$matches = null,
            int $flags = 0,
            int $offset = 0
        ): int|false
        {
            $subject = $subject?? '';

            return preg_match($pattern, $subject, $matches, $flags, $offset);
        }
    }
}
