<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationIsRestricted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('register*')) {
            if ($request->isMethod('GET')) {
                return redirect()->route('access-requests.create');
            }

            abort(403, 'Public registration is disabled. Please submit an access request to be approved by an administrator.');
        }

        return $next($request);
    }
}
