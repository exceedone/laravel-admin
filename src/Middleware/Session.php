<?php

namespace Encore\Admin\Middleware;

use Illuminate\Http\Request;

class Session
{
    /**
     * @param Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        // get baseUrl
        config(['session.path' => $this->getSessionPath($request)]);

        if ($domain = config('admin.route.domain')) {
            config(['session.domain' => $domain]);
        }

        return $next($request);
    }

    /**
     * Get session path
     *
     * @return string|null
     */
    protected function getSessionPath(Request $request) : ?string
    {
        // get baseUrl
        $baseUrl = trim(request()->getBaseUrl(), '/');
        $path = '';
        
        if(!empty($baseUrl)){
            $path .= '/'.$baseUrl;
        }else{
            $path = '';
        }

        // @phpstan-ignore-next-line $string is always string at runtime
        $path .= '/' . trim(config('admin.route.prefix'), '/');
        return $path;
    }
}
