<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeLocale = $request->route('locale');
        $locale = is_string($routeLocale) ? $routeLocale : null;

        if (! in_array($locale, ['ru', 'uz'], true)) {
            $sessionLocale = $request->session()->get('locale');
            $locale = is_string($sessionLocale) ? $sessionLocale : null;
        }

        if (! in_array($locale, ['ru', 'uz'], true)) {
            $cookieLocale = $request->cookie('locale');
            $locale = is_string($cookieLocale) ? $cookieLocale : null;
        }

        if (! in_array($locale, ['ru', 'uz'], true)) {
            $locale = 'ru';
        }

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);
        $request->session()->put('locale', $locale);
        Cookie::queue(Cookie::forever('locale', $locale));

        return $next($request);
    }
}
