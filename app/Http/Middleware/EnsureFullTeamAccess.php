<?php

namespace App\Http\Middleware;

use App\Support\TeamAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFullTeamAccess
{
    /**
     * @var array<int, string>
     */
    protected array $restrictedRoutePatterns = [
        'teams.*',
        'profile.show',
        'evidencija.export',
        'evidencija.export.*',
        'evidencija.print',
        'pdf.*',
        'doko.*',
        'gio1.download',
        'api.tokens.*',
    ];

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || TeamAccess::hasFullTeamAccess($user)) {
            return $next($request);
        }

        if ($request->routeIs($this->restrictedRoutePatterns)) {
            return redirect()
                ->route('dashboard')
                ->with('notify', [
                    'message' => 'Nemate dozvolu za pristup ovoj stranici.',
                    'type' => 'error',
                ]);
        }

        return $next($request);
    }
}
