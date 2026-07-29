<?php

namespace App\Policies;

use App\Models\GradjevinskiDkoZahtev;
use App\Models\User;
use App\Support\TeamAccess;
use Illuminate\Auth\Access\HandlesAuthorization;

class GradjevinskiDkoZahtevPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->currentTeam !== null;
    }

    public function view(User $user, GradjevinskiDkoZahtev $zahtev): bool
    {
        return TeamAccess::canAccessTeam($user, $zahtev->team);
    }

    public function create(User $user): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        return $user->currentTeam !== null
            && TeamAccess::canCreateEvidencija($user);
    }

    public function delete(User $user, GradjevinskiDkoZahtev $zahtev): bool
    {
        if ($zahtev->status !== GradjevinskiDkoZahtev::STATUS_NA_CEKANJU) {
            return false;
        }

        if ($user->is_super_admin) {
            return true;
        }

        return (int) $zahtev->kreirao_korisnik_id === (int) $user->id
            || TeamAccess::canAccessTeam($user, $zahtev->team);
    }

    public function manage(User $user, GradjevinskiDkoZahtev $zahtev): bool
    {
        return $user->is_super_admin === true;
    }
}
