<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class SuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Gate::allows('super-admin')) {
            abort(403, 'Nemate pristup admin panelu.');
        }

        return $next($request);
    }
}
