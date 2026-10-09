<?php

namespace Encore\Admin\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // @phpstan-ignore-next-line $path is always string|null at runtime
        $redirectTo = admin_base_path(config('admin.auth.redirect_to', 'auth/login'));

        if (Auth::guard('admin')->guest() && !$this->shouldPassThrough($request)) {
            return redirect()->guest($redirectTo);
        }

        return $next($request);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        $excepts = config('admin.auth.excepts', [
            'auth/login',
            'auth/logout',
        ]);
        /** @phpstan-ignore-next-line Unable to resolve the template type TKey in call to function collect */
        return collect($excepts)
            // @phpstan-ignore-next-line The callback matches the expected signature at runtime
            ->map('admin_base_path')
            ->contains(function ($except) use ($request) {
                if ($except !== '/') {
                    $except = trim($except, '/');
                }

                return $request->is($except);
            });
    }
}
