<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasTeam
{
    /**
     * Ensure the user has at least one firm (team) and an active current team.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->is_super_admin) {
            if ($user->allTeams()->isEmpty() && $request->routeIs('dashboard', 'teams.create')) {
                return redirect()->route('admin.dashboard');
            }

            return $next($request);
        }

        if ($user->allTeams()->isEmpty()) {
            if (! $request->routeIs('teams.create')) {
                return redirect()->route('teams.create');
            }

            return $next($request);
        }

        if (! $user->currentTeam) {
            $user->switchTeam($user->allTeams()->first());
            $user->refresh();
        }

        return $next($request);
    }
}
