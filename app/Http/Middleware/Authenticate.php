<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // محاولة اكتشاف الحارس من الميدلوير في الراوت
        $middlewares = $request->route()?->gatherMiddleware() ?? [];
        foreach ($middlewares as $mw) {
            if (is_string($mw) && str_starts_with($mw, 'auth:')) {
                $guard = substr($mw, strlen('auth:'));
                if ($guard === 'admin') {
                    return route('admin.login');
                }
            }
        }

        // توجيه حسب المسار لمسارات الأدمن
        $path = trim($request->path(), '/');
        if (str_starts_with($path, 'admin') || $request->routeIs('admin.*')) {
            return route('admin.login');
        }

        return route('login');
    }
}
