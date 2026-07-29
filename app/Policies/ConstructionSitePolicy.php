<?php

namespace App\Policies;

use App\Models\ConstructionSite;
use App\Models\User;
use App\Support\TeamAccess;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConstructionSitePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->currentTeam !== null;
    }

    public function view(User $user, ConstructionSite $site): bool
    {
        return TeamAccess::canAccessTeam($user, $site->team);
    }

    public function create(User $user): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        return $user->currentTeam !== null
            && TeamAccess::canCreateEvidencija($user);
    }

    public function update(User $user, ConstructionSite $site): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        return TeamAccess::canAccessTeam($user, $site->team)
            && TeamAccess::canCreateEvidencija($user, $site->team);
    }

    public function delete(User $user, ConstructionSite $site): bool
    {
        return $user->is_super_admin === true;
    }

    public function zavrsi(User $user, ConstructionSite $site): bool
    {
        return $this->update($user, $site);
    }
}
