<?php

namespace App\Support;

use App\Models\Team;

class AdminTeamContext
{
    public const SESSION_KEY = 'admin_selected_team_id';

    public static function selectedTeamId(): ?int
    {
        $id = session(self::SESSION_KEY);

        return $id ? (int) $id : null;
    }

    public static function selectedTeam(): ?Team
    {
        $id = self::selectedTeamId();

        return $id ? Team::find($id) : null;
    }

    public static function select(int $teamId): void
    {
        abort_unless(Team::whereKey($teamId)->exists(), 404);

        session([self::SESSION_KEY => $teamId]);
    }

    public static function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}
