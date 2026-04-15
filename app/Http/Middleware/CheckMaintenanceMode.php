<?php

namespace App\Http\Middleware;

use App\Models\GlobalConfig;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (GlobalConfig::MaintenanceMode() === 1) {
            $path = $request->path();

            $isAdminRoute = str_starts_with($path, 'admin') || str_starts_with($path, 'livewire');
            
            if (!$isAdminRoute) {
                return response()->view('errors.maintenance', [], 503);
            }
        }

        return $next($request);
    }
}
