<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
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
        // Get locale from header, query parameter, or session
        $locale = $request->header('Accept-Language') ?? 
                 $request->query('locale') ?? 
                 session('locale', 'en');

        // Extract primary language code
        $locale = explode(',', $locale)[0];
        $locale = explode('-', $locale)[0];

        // Validate and set locale
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
            session()->put('locale', $locale);
        } else {
            app()->setLocale('en');
            session()->put('locale', 'en');
        }

        // Update user's language preference if authenticated
        $user = User::auth();
        if ($user && $user->language !== $locale) {
            $user->language = $locale;
            $user->save();
        }

        return $next($request);
    }
}
