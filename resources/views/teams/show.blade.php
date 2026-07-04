<x-layouts.dashboard title="Podešavanja firme">
    <div class="max-w-4xl space-y-8">
        @livewire('teams.update-team-company-form', ['team' => $team])

        @livewire('teams.team-member-manager', ['team' => $team])

        @if (Gate::check('delete', $team) && ! $team->personal_team)
            @livewire('teams.delete-team-form', ['team' => $team])
        @endif
    </div>
</x-layouts.dashboard>
