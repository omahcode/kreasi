<?php

namespace App\Http\Middleware;

use App\Models\TrafficLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackTraffic
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        $path = '/'.ltrim($request->path(), '/');

        if ($this->shouldIgnore($path)) {
            return;
        }

        try {
            TrafficLog::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'method' => $request->method(),
                'path' => substr($path, 0, 255),
                'route_name' => $request->route()?->getName(),
                'status_code' => $response->getStatusCode(),
            ]);
        } catch (\Throwable $e) {
            // Silently ignore so tracking never disrupts requests
        }
    }

    protected function shouldIgnore(string $path): bool
    {
        $ignoredPrefixes = [
            '/assets',
            '/build',
            '/favicon',
            '/sw.js',
            '/manifest.json',
            '/up',
            '/_ignition',
            '/livewire/livewire.js',
        ];

        foreach ($ignoredPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
