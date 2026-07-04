<?php

namespace App\Livewire\Admin;

use App\Models\Team;
use App\Support\AdminTeamContext;
use Illuminate\Support\Collection;
use Livewire\Component;

class AdminTeamSelector extends Component
{
    public ?int $selectedTeamId = null;

    public function mount(): void
    {
        $this->selectedTeamId = AdminTeamContext::selectedTeamId();
    }

    public function updatedSelectedTeamId(mixed $value): void
    {
        if ($value === '' || $value === null) {
            AdminTeamContext::clear();
            $this->selectedTeamId = null;
        } else {
            AdminTeamContext::select((int) $value);
            $this->selectedTeamId = (int) $value;
        }

        $this->dispatch('admin-team-changed', teamId: $this->selectedTeamId);
    }

    public function getTeamsProperty(): Collection
    {
        return Team::query()
            ->orderBy('name')
            ->get(['id', 'name', 'pib', 'tip_subjekta']);
    }

    public function render()
    {
        return view('livewire.admin.admin-team-selector', [
            'teams' => $this->teams,
            'selectedTeam' => AdminTeamContext::selectedTeam(),
        ]);
    }
}
