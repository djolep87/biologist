<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamCompany;
use App\Actions\Jetstream\UpdateTeamName;
use App\Livewire\Teams\CreateTeamForm;
use App\Livewire\Teams\TeamMemberManager;
use App\Livewire\Teams\UpdateTeamCompanyForm;
use App\Support\TeamAccess;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Livewire\Livewire;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Livewire::component('teams.create-team-form', CreateTeamForm::class);
        Livewire::component('teams.update-team-company-form', UpdateTeamCompanyForm::class);
        Livewire::component('teams.team-member-manager', TeamMemberManager::class);

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamCompany::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role(TeamAccess::ROLE_ADMIN, 'Administrator firme', [
            'team:manage',
            'evidencija:manage',
            'dokumenti:manage',
            'zahtevi:manage',
            'gio1:manage',
        ])->description('Puni pristup firmi — evidencija, dokumenti, zahtevi i podešavanja.');

        Jetstream::role(TeamAccess::ROLE_EVIDENCIAR, 'Evidenciar', [
            'evidencija:create',
            'evidencija:read',
        ])->description('Može samo da unosi dnevne izveštaje o otpadu.');
    }
}
