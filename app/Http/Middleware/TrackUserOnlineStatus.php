<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackUserOnlineStatus
{
    /**
     * Menangani request untuk mencatat heartbeat online pengguna ke dalam Cache.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userId = Auth::id();
            // Cache heartbeat selama 5 menit tanpa membebani penulisan ke database
            Cache::put('user-online-' . $userId, now(), now()->addMinutes(5));
        }

        return $next($request);
    }
}
