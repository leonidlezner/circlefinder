<?php

namespace App\Http\Middleware;

use Closure;

class CheckTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (is_null($user->timezone) || strlen($user->timezone) < 1) {
            # Store the url, so we can redirect later
            session()->put('url.intended', url()->current());

            return redirect(route('profile.timezone.edit'))
                ->withErrors('Please set your timezone to continue using CircleFinder!');
        }

        return $next($request);
    }
}
