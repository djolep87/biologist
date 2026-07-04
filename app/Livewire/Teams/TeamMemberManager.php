<?php

namespace App\Livewire\Teams;

use App\Actions\Jetstream\CreateTeamMember;
use App\Support\TeamAccess;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\Actions\UpdateTeamMemberRole;
use Laravel\Jetstream\Contracts\RemovesTeamMembers;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Role;
use Livewire\Component;

class TeamMemberManager extends Component
{
    public $team;

    public bool $currentlyManagingRole = false;

    public $managingRoleFor;

    public ?string $currentRole = null;

    public bool $confirmingLeavingTeam = false;

    public bool $confirmingTeamMemberRemoval = false;

    public ?int $teamMemberIdBeingRemoved = null;

    public ?string $createdMemberPassword = null;

    public ?string $createdMemberEmail = null;

    public bool $createdMemberWasNew = false;

    public array $addTeamMemberForm = [
        'name' => '',
        'email' => '',
        'role' => TeamAccess::ROLE_EVIDENCIAR,
        'password' => '',
    ];

    public function mount($team): void
    {
        $this->team = $team;
    }

    public function addTeamMember(CreateTeamMember $creator): void
    {
        $this->resetErrorBag();
        $this->createdMemberPassword = null;
        $this->createdMemberEmail = null;
        $this->createdMemberWasNew = false;

        $result = $creator->create($this->user, $this->team, $this->addTeamMemberForm);

        $this->createdMemberEmail = $this->addTeamMemberForm['email'];
        $this->createdMemberPassword = $result['generated_password'];
        $this->createdMemberWasNew = $result['was_new_user'];

        $this->addTeamMemberForm = [
            'name' => '',
            'email' => '',
            'role' => TeamAccess::ROLE_EVIDENCIAR,
            'password' => '',
        ];

        $this->team = $this->team->fresh();

        $this->dispatch('saved');
    }

    public function cancelTeamInvitation($invitationId): void
    {
        if (! empty($invitationId)) {
            $model = Jetstream::teamInvitationModel();
            $foreignKey = (new $model)->team()->getForeignKeyName();

            $model::whereKey($invitationId)
                ->where($foreignKey, $this->team->id)
                ->delete();
        }

        $this->team = $this->team->fresh();
    }

    public function manageRole($userId): void
    {
        $this->currentlyManagingRole = true;
        $this->managingRoleFor = Jetstream::findUserByIdOrFail($userId);
        $this->currentRole = $this->managingRoleFor->teamRole($this->team)->key;
    }

    public function updateRole(UpdateTeamMemberRole $updater): void
    {
        $updater->update(
            $this->user,
            $this->team,
            $this->managingRoleFor->id,
            $this->currentRole
        );

        $this->team = $this->team->fresh();
        $this->stopManagingRole();
    }

    public function stopManagingRole(): void
    {
        $this->currentlyManagingRole = false;
    }

    public function leaveTeam(RemovesTeamMembers $remover)
    {
        $remover->remove(
            $this->user,
            $this->team,
            $this->user
        );

        $this->confirmingLeavingTeam = false;
        $this->team = $this->team->fresh();

        return redirect(config('fortify.home'));
    }

    public function confirmTeamMemberRemoval($userId): void
    {
        $this->confirmingTeamMemberRemoval = true;
        $this->teamMemberIdBeingRemoved = $userId;
    }

    public function removeTeamMember(RemovesTeamMembers $remover): void
    {
        $remover->remove(
            $this->user,
            $this->team,
            Jetstream::findUserByIdOrFail($this->teamMemberIdBeingRemoved)
        );

        $this->confirmingTeamMemberRemoval = false;
        $this->teamMemberIdBeingRemoved = null;
        $this->team = $this->team->fresh();
    }

    public function getUserProperty()
    {
        return Auth::user();
    }

    public function getRolesProperty(): array
    {
        return collect(Jetstream::$roles)
            ->filter(fn ($role) => in_array($role->key, [
                TeamAccess::ROLE_ADMIN,
                TeamAccess::ROLE_EVIDENCIAR,
            ], true))
            ->transform(function ($role) {
                return with($role->jsonSerialize(), function ($data) {
                    return (new Role(
                        $data['key'],
                        $data['name'],
                        $data['permissions']
                    ))->description($data['description']);
                });
            })
            ->values()
            ->all();
    }

    public function render()
    {
        return view('teams.team-member-manager');
    }
}
