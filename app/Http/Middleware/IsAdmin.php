<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class IsAdmin
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
        $user = $request->user();
        if (!$user->is_admin) {
            return response()->json(['message' => 'not an admin user'], 401);
        }

        return $next($request);
    }
}
