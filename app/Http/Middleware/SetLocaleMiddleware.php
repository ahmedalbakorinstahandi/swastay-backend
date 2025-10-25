<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\MessageService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = explode(',', $request->header('Accept-Language', 'en'))[0];
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        // MessageService::abort(400, $locale);

        // Store authenticated user in cache for this request
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            $token = $request->bearerToken();
            if ($token) {
                $cacheKey = 'request_user_' . $token;

                // Store or update user in cache with TTL refresh
                cache()->put($cacheKey, $user, 300); // 5 minutes - refreshes TTL if exists
            }
        }


        $user = User::auth();
        if ($user) {

            if ($user->language != $locale) {
                $user->language = $locale;
                $user->save();
            }
        }

        return $next($request);
    }
}
