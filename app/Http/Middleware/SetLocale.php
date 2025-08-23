<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = ['en', 'sq', 'de'];
        
        // Check if locale is provided in the request
        if ($request->has('locale') && in_array($request->get('locale'), $availableLocales)) {
            $locale = $request->get('locale');
            Session::put('locale', $locale);
        } else {
            // Get locale from session or use default
            $locale = Session::get('locale', config('app.locale'));
        }
        
        // Ensure the locale is valid
        if (!in_array($locale, $availableLocales)) {
            $locale = config('app.locale');
        }
        
        App::setLocale($locale);
        
        return $next($request);
    }
}
