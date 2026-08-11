<?php

namespace App\Support;

use App\Models\Team;
use App\Models\User;

class TeamAccess
{
    public const ROLE_ADMIN = 'admin';

    public const ROLE_EVIDENCIAR = 'evidenciar';

    public static function canAccessTeam(User $user, Team|int|null $team): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        if (is_int($team)) {
            $team = Team::find($team);
        }

        if (! $team) {
            return false;
        }

        return $user->belongsToTeam($team);
    }

    public static function teamRoleKey(User $user, ?Team $team = null): ?string
    {
        $team ??= $user->currentTeam;

        if (! $team) {
            return null;
        }

        if ($user->ownsTeam($team)) {
            return self::ROLE_ADMIN;
        }

        return $user->teamRole($team)?->key;
    }

    public static function isEvidenciarOnly(User $user, ?Team $team = null): bool
    {
        return self::teamRoleKey($user, $team) === self::ROLE_EVIDENCIAR;
    }

    public static function hasFullTeamAccess(User $user, ?Team $team = null): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        $team ??= $user->currentTeam;

        if (! $team) {
            return false;
        }

        if ($user->ownsTeam($team)) {
            return true;
        }

        $role = self::teamRoleKey($user, $team);

        return in_array($role, [self::ROLE_ADMIN, 'editor'], true);
    }

    public static function canManageTeam(User $user, ?Team $team = null): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        $team ??= $user->currentTeam;

        if (! $team) {
            return false;
        }

        if ($user->ownsTeam($team)) {
            return true;
        }

        return $user->hasTeamPermission($team, 'team:manage');
    }

    public static function canManageEvidencija(User $user, ?Team $team = null): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        $team ??= $user->currentTeam;

        if (! $team || ! $user->belongsToTeam($team)) {
            return false;
        }

        if (self::hasFullTeamAccess($user, $team)) {
            return true;
        }

        return $user->hasTeamPermission($team, 'evidencija:create')
            || $user->hasTeamPermission($team, 'evidencija:read');
    }

    public static function canCreateEvidencija(User $user, ?Team $team = null): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        $team ??= $user->currentTeam;

        if (! $team || ! $user->belongsToTeam($team)) {
            return false;
        }

        if (self::hasFullTeamAccess($user, $team)) {
            return true;
        }

        return $user->hasTeamPermission($team, 'evidencija:create');
    }

    public static function canAccessEvidencija(User $user, int $teamId): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        return self::canManageEvidencija($user, $user->teams()->find($teamId));
    }
}
