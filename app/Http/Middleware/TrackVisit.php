<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;

class TrackVisit
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && ! $request->ajax()) {
            Visit::create([
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                'visited_at' => now(),
            ]);
        }

        return $next($request);
    }
}
